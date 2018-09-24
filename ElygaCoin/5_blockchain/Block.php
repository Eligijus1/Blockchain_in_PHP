<?php

class Block
{
    public $previous;
    public $hash;
    public $message;
    
    public function __construct($message, ?Block $previous) {//NOTE: Genesis block will have no previous
        $this->previous = $previous ? $previous->hash : null;
        $this->message = $message;
        $this->mine();
    }
    
    public function mine() {
        $data = $this->message.$this->previous;
        $this->nonce = Pow::findNonce($data);
        $this->hash = Pow::hash($data.$this->nonce);
    }
    
    public function isValid() {
        return Pow::isValidNonce($this->message.$this->previous, $this->nonce);
    }
    
    public function _toString(): string {
        return sprintf("Previous: %s\Nonce: %s\nHash: %s\nMessage: %s", $this->previous, $this->nonce, $this->hash, $this->message);
    }
}

