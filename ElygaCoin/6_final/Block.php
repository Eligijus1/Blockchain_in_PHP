<?php

namespace ElygaCoinFinal;

class Block
{
    public $previousHash;
    public $nonce;
    public $hash;
    public $transaction;

    public function __construct(Transaction $transaction, ?Block $previousBlock)
    {//NOTE: Genesis block will have no previous
        $this->previousHash = $previousBlock ? $previousBlock->hash : null;
        $this->transaction = $transaction;
        $this->mine();
    }

    public static function createGenesis(string $publicKey, string $privateKey, int $amount)
    {
        return new self(new Transaction(null, $publicKey, $amount, $privateKey), null);
    }

    public function mine()
    {
        $data = $this->transaction->message() . $this->previousHash;
        $this->nonce = Pow::findNonce($data);
        $this->hash = Pow::hash($data . $this->nonce);
    }

    public function isValid(): bool
    {
        return Pow::isValidNonce($this->transaction->message() . $this->previousHash, $this->nonce);
    }

    public function __toString(): string
    {
        return sprintf(
            "Previous: %s\nNonce: %s\nHash: %s\nMessage: %s",
            $this->previousHash,
            $this->nonce,
            $this->hash,
            $this->transaction->message()
        );
    }
}

