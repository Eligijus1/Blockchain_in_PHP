<?php

class State
{
    public $state;

    private $file;
    private $user;
    private $port;
    private $peerPort;

    /**
     * @inheritdoc
     */
    public function __construct($user, $port = null, $peerPort = null)
    {
        $this->user = $user;
        $this->port = $port;
        $this->peerPort = $peerPort;

        $this->file = __DIR__ . '/data/' . trim($user) . '.json';

        if ($this->port && !isset($this->state[$this->peerPort])) {
            $this->state[$this->peerPort] = ['user' => '', 'coins' => '', 'version' => 0];
        }
        if ($this->port && !isset($this->state[$this->port])) {
            $this->updateMine();
        }
        $this->reload();
    }

    /**
     * Entry point for node.
     */
    public function loop()
    {
        $i = 0;
        $j = 0;
        while (true) {
            printf("\033[37;40m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Current {$this->user} state:\033[39;49m\n%s\n", $this);
            foreach ($this->state as $p => $data) {
                if ($p == $this->port) {
                    continue;
                }
                $j++;
                $data = json_encode($this->state);
                $peerState = @file_get_contents('http://localhost:' . $p . '/gossip', false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'ignore_errors' => 1,
                        'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\n",
                        'content' => $data
                    ]
                ]));

                //DEBUG:
                //echo "\n{$j}. Ping peer {$p}. Peer state: {$peerState}.\n";
                //echo "\n{$j}. Ping peer {$p}. State: {$peerState}. Data: {$data}\n";
                //return;
                //echo "\n{$j}. Ping peer {$p}.\n";

                if (!$peerState) {
                    unset($this->state[$p]);
                    $this->save();
                } else {
                    $this->update(json_decode($peerState, true));
                }
            }

            $this->reload();
            usleep(rand(300000, 3000000));
            if (++$i % 2) {
                $coins = $this->updateMine();
                printf("\033[37;40m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " {$this->user} own coins updated to {$coins}.\033[39;49m\n");
            }
        }
    }

    public function reload()
    {
        $this->state = (file_exists($this->file) ? json_decode(file_get_contents($this->file, true), true) : []);

        /*
        echo "DEBUG 1: {$this->file}\n";
        echo "DEBUG 2: " . file_exists($this->file) . "\n";
        print_r($this->state);
        */
    }

    public function update($state)
    {
        if (!$state) {
            return;
        }

        foreach ($state as $port => $data) {
            if ($port = $this->port) {
                continue;
            }
            if (!isset($data['user']) || !isset($data['coins']) || !isset($data['version'])) {
                continue;
            }
            if (!isset($this->state[$port]) || (int)$data['version'] > (int)$this->state[$port]['version']) {
                $this->state[$port] = $data;
            }
        }
        $this->save();
    }

    public function updateMine()
    {
        $coins = $this->randomNumber();
        $version = $this->incrementVersion();
        $this->state[$this->port] = ['user' => $this->user, 'coins' => $coins, 'version' => $version];
        $this->save();
        
        return $coins;
    }

    public function __toString()
    {
        $data = [];
        foreach ($this->state as $port => $d) {
            $data[] = sprintf(date_format(new \DateTime(), 'Y.m.d H:i:s') . " %s/%s -- %d/%s", $port, $d['user'], $d['version'], $d['coins']);
        }
        return implode("\n", $data);
    }

    public function save()
    {
        file_put_contents($this->file, json_encode($this->state));
    }

    public function incrementVersion()
    {
        return isset($this->state['version']) ? (int)($this->state['version']) + 1 : 1;
    }

    public function randomNumber()
    {
        return rand(0, 100);
    }
}
