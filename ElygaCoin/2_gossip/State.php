<?php

class State
{
    public $state;

    private $file;
    private $user;
    private $port;
    private $peerPort;
    private $sessions;

    /**
     * @inheritdoc
     */
    public function __construct($user, $port = null, $peerPort = null)
    {
        $this->user = $user;
        $this->port = $port;
        $this->peerPort = $peerPort;

        $this->sessions = explode("\n", file_get_contents(__DIR__ . '/data/sessions.txt'));
        $this->file = __DIR__ . '/data/' . $user . '.json';
        if ($this->port && !isset($this->state[$this->peerPort])) {
            $this->state[$this->peerPort] = ['user' => '', 'session' => '', 'version' => 0];
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
        while (true) {
            printf("\033[37;40m..Current state..\033[39;49m\n%s\n", $this);
            foreach ($this->state as $p => $data) {
                if ($p == $this->port) {
                    continue;
                }
                $data = json_encode($this->state);
                $peerState = @file_get_contents('http://localhost' . $p . '/gossip', null, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\n",
                        'content' => $data
                    ]
                ]));
                if ($peerState) {
                    unset($this->state[$p]);
                    $this->save();
                } else {
                    $this->update(json_decode($peerState, true));
                }
            }
        }
        $this->reload();
        usleep(rand(300000, 3000000));
        if (++$i % 2) {
            $this->updateMine();
            printf("\033[37;40m..Session updated..\033[39;49m\n");
        }
    }

    public function reload()
    {
        $this->state = file_exists($this->file) ? json_decode(file_get_contents($this->file, true)) : [];
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
            if (!isset($data['user']) || $data['version'] > $this->state[$port]['version']) {
                $this->state[$port] = $data;
            }
        }
        $this->save();
    }

    public function updateMine()
    {
        $session = $this->randomNumber();
        $version = $this->incrementVersion();
        $this->state = ['user' => $this->user, 'session' => $session, 'version' => $version];
        $this->save();
    }

    public function __toString()
    {
        $data = [];
        foreach ($this->state as $port => $d) {
            $data[] = sprintf('%s/%s -- %d/%s', $port, $d['user'], $d['version'], substr($d['session'], 0, 40));
        }
        return implode("\n", $data);
    }

    public function save()
    {
        file_put_contents($this->file, json_encode($this->state));
    }

    public function incrementVersion()
    {
        return $this->state['version'] + 1;
    }

    public function randomNumber()
    {
        return rand(0, 100);
    }
}
