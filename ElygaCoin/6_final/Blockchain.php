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

    public function add(Transaction $message)
    {
        $this->blocks[] = new Block($message, $this->blocks[count($this->blocks) - 1]);
    }

    public function isValid(): bool
    {
        foreach ($this->blocks as $i => $block) {
            if (!$block->isValid()) {
                error_log("\e[0;31mERROR: Block {$i} is not valid.\e[0m");
                return false;
            }
            if ($i != 0 && $this->blocks[$i - 1]->hash != $block->previousHash) {
                error_log("\e[0;31mERROR: Block {$i} wrong hash.\e[0m");
                return false;
            }
        }
        $spendsValid = $this->areSpendsValid();
        if (!$spendsValid) {
            error_log("\e[0;31mERROR: Block chain spend not valid.\e[0m");
        }
        return $spendsValid;
    }

    private function areSpendsValid(): bool
    {
        $balances = $this->computeBalances();
        foreach ($balances as $publicKey => $amount) {
            if ($amount < 0) {
                error_log("\e[0;31mERROR: Block chain has wrong balance.\e[0m");
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
        if (!$peerBlockChain->isValid()) {
            error_log("\e[0;31mERROR: Peer blockchain not valid.\e[0m");
            return;
        }
        $this->blocks = $peerBlockChain->blocks;
    }

    public function balancesAsString()
    {
        $data = [];
        foreach ($this->computeBalances() as $publicKey => $amount) {
            $data[] = sprintf("%s = %s coins", substr($publicKey, 72, 7), $amount);
        }

        return implode("\n", $data);
    }

    public function __toString(): string
    {
        return implode("\n\n", $this->blocks);
    }


}

