<?php

namespace ElygaCoinFinal;

class Gossip
{
    private $name;
    private $key;
    private $state;
    private $port;

    /**
     * @inheritdoc
     */
    public function __construct($name, $port, $peerPort = null)
    {
        $this->name = trim($name);
        $this->port = trim($port);

        $this->key = new Key($this->name);
        $peers = [$this->port => true];

        if (!$peerPort) {
            $blockChain = new Blockchain($this->key->publicKey, $this->key->privateKey, 1000000);
        } else {
            $blockChain = null;
            $peers[trim($peerPort)] = true;
        }

        $this->state = new State($name, $blockChain, $peers);
        //$this->state = State::createState($name, $blockChain, $peers);
    }

    public function loop()
    {
        while (true) {
            print("\n\033[37;40m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " --Networks--:\033[39;49m\n");
            foreach (array_keys($this->state->peers) as $port) {
                //print("loop: {$port}\n");
                if ($port == $this->port) {
                    continue;
                }
                //print("Gossip with {$port}\n");
                $this->withPeer($port);
            }
            $this->state->reload();
            $this->displayState();
            usleep(rand(300000, 3000000));
        }
    }

    public function withPeer($port)
    {
        $peerState = $this->gossip($port);
        if (!$peerState) {
            unset($this->state->peers[$port]);
            $this->state->save();
        } else {
            $this->state->update($peerState);
        }
    }

    private function gossip($port): ?State
    {
        if ($port == $this->port) {
            printf("\e[0;31m" . date_format(new \DateTime(),
                    'Y.m.d H:i:s') . " gossip invoked with own {$port}.\e[0m\n");
            return null;
        }

        $data = base64_encode(serialize($this->state));
        $peerState = @file_get_contents('http://localhost:' . $port . '/gossip', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\n",
                'content' => $data
            ]
        ]));

        $state = null;
        if ($peerState) {
            $peerStateDecoded = base64_decode($peerState);
            $state = unserialize($peerStateDecoded);
            if ($state instanceof State) {
                printf("\e[0;32m" . date_format(new \DateTime(),
                        'Y.m.d H:i:s') . " gossip {$port} returned correct state.\e[0m\n");
            } else {
                $state = null;
                printf("\e[0;31m" . date_format(new \DateTime(),
                        'Y.m.d H:i:s') . " gossip {$port} returned wrong state.\e[0m\n");
            }
        }

        return $state;
    }

    private function displayState()
    {
        $peersPortsString = "";
        $balancesAsString = "";

        foreach (array_keys($this->state->peers) as $port) {
            if ($port == $this->port) {
                continue;
            }
            $peersPortsString .= ($peersPortsString ? "," : "");
            $peersPortsString .= $port;
        }

        if ($this->state->blockChain) {
            $balancesAsString = $this->state->blockChain->balancesAsString();
        }

        //print("\n{$this->name} peers: {$peersPortsString} and balances: \n" . $balancesAsString);
        print("\n\e[0;34m" . date_format(new \DateTime(),
                'Y.m.d H:i:s') . " {$this->name} peers: {$peersPortsString} and balances: \n{$balancesAsString}\e[0m\n");
    }
}
