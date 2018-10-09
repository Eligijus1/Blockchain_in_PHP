<?php

namespace ElygaCoinFinal;

class Blockchain implements \Countable
{
    /** @var Block[] */
    public $blocks = [];

    public function __construct($publicKey, $privateKey, $amount)
    {
        $this->blocks[] = Block::createGenesis($publicKey, $privateKey, $amount);
    }

    public function count()
    {
        return count($this->blocks);
    }

    public function add($message)
    {
        $this->blocks[] = new Block($message, $this->blocks[count($this->blocks) - 1]);
    }

    public function isValid(): bool
    {
        foreach ($this->blocks as $i => $block) {
            if (!$block->isValid()) {
                return false;
            }
            if ($i != 0 && $this->blocks[$i - 1]->hash != $block->previousHash) {
                return false;
            }
        }
        return $this->areSpendsValid();
    }

    private function areSpendsValid(): bool
    {
        $balances = $this->computeBalances();
        foreach ($balances as $publicKey => $amount) {
            if ($amount < 0) {
                return false;
            }
        }
        return true;
    }

    private function computeBalances()
    {
        $genesisTransaction = $this->blocks[0]->transaction;
        $balances = [$genesisTransaction->to => $genesisTransaction->amount];
        foreach ($this->blocks as $i => $block) {
            if (0 === $i) {
                continue;
            }
            if (!isset($balances[$block->transaction->from])) {
                $balances[$block->transaction->from] = 0;
            }
            $balances[$block->transaction->from] -= $block->transaction->amount;
            if (!isset($balances[$block->transaction->to])) {
                $balances[$block->transaction->to] = 0;
            }
            $balances[$block->transaction->to] += $block->transaction->amount;
        }
        return $balances;
    }

    public function update(?self $peerBlockChain)
    {
        if (null === $peerBlockChain) {
            return;
        }
        if (count($peerBlockChain) <= count($this)) {
            return;
        }
        if ($peerBlockChain->isValid()) {
            return;
        }
        $this->blocks = $peerBlockChain->blocks;
    }

    public function balancesAsString()
    {
        $data = [];
        foreach ($this->computeBalances() as $publicKey => $amount) {
            $data[] = sprintf("%s = %s ElygaCoins", substr($publicKey, 72, 7), $amount);
        }

        return implode("\n", $data);
    }

    public function __toString(): string
    {
        return implode("\n\n", $this->blocks);
    }


}

