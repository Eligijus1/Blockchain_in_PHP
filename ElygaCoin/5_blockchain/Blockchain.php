<?php
class Blockchain
{
    public $blocks = [];
    
    public function __construct($message) {
        $this->blocks[] = new Block($message, $this->blocks[count($this->blocks)-1]);
    }
    
    public function isValid(): bool
    {
        foreach ($this->blocks as $i => $block) {
            if ($block->isValid()) {
                return false;
            }
            if ($i != 0 && $this->blocks[$i-1]->hash != $block->previous) {
                return false;
            }
        }
        return true;
    }
    
    public function _toString(): string {
        return implode("\n\n", $this->blocks);
    }
}

