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
        $this->key = new Key($name);
        $peers = [$port => true];

        if (!$peerPort) {
            $blockChain = new Blockchain($this->key->publicKey, $this->key->privateKey, 1000000);
        } else {
            $blockChain = null;
            $peers[$peerPort] = true;
        }
        $this->state = new State($name, $blockChain, $peers);
    }

    public function loop()
    {
        while (true) {
            print("\n\033[37;40m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " --Networks--:\033[39;49m\n");
            foreach (array_keys($this->state->peers) as $port) {
                if ($port == $this->port) {
                    continue;
                }
                printf("Gossip with %d", $port);
                $this->withPeer($port);
            }
            $this->displayState();
            $this->state->reload();
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
        $data = base64_encode(serialize($this->state));
        $peerState = @file_get_contents('http://localhost:' . $port . '/gossip', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'ignore_errors' => 1,
                'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\n",
                'content' => $data
            ]
        ]));

        return unserialize(base64_decode($peerState));
    }

    private function displayState()
    {
        $peersArray = [];
        $balancesAsString = "";

        foreach (array_keys($this->state->peers) as $peer) {
            $peersArray[] = $peer;
        }

        if ($this->state->blockChain) {
            $balancesAsString = $this->state->blockChain->balancesAsString();
        }

        print("\n{$this->name} peers: " . implode($peersArray, ",") . "; balances: \n" . $balancesAsString);
    }
}
