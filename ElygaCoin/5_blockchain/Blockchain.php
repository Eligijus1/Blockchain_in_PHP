<?php

namespace ElygaCoin;

class Blockchain
{
    /** @var Block[] */
    public $blocks = [];
    
    public function __construct($message) {
        $this->blocks[] = new Block($message, null);
    }

    public function add($message) {
        $this->blocks[] = new Block($message, $this->blocks[count($this->blocks)-1]);
    }
    
    public function isValid(): bool
    {
        foreach ($this->blocks as $i => $block) {
            if (!$block->isValid()) {
                return false;
            }
            if ($i != 0 && $this->blocks[$i-1]->hash != $block->previousHash) {
                return false;
            }
        }
        return true;
    }
    
    public function __toString(): string {
        return implode("\n\n", $this->blocks);
    }
}

