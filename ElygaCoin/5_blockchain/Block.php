<?php

namespace ElygaCoin;

class Block
{
    public $previousHash = "";
    public $hash = "";
    public $message = "";
    public $nonce;

    public function __construct($message, ?Block $previousBlock)
    {//NOTE: Genesis block will have no previous
        $this->previousHash = $previousBlock ? $previousBlock->hash : null;
        $this->message = $message;
        $this->mine();
    }

    public function mine()
    {
        $data = $this->message . $this->previousHash;
        $this->nonce = Pow::findNonce($data);
        $this->hash = Pow::hash($data . $this->nonce);
    }

    public function isValid(): bool
    {
        return Pow::isValidNonce($this->message . $this->previousHash, $this->nonce);
    }

    public function __toString(): string
    {
        return sprintf(
            "Previous: %s\nNonce: %s\nHash: %s\nMessage: %s",
            $this->previousHash,
            $this->nonce,
            $this->hash,
            $this->message
        );
    }
}

