<?php

namespace Blockchain\Service;

use Blockchain\Model\Block;

/**
 * Class BlockManager
 *
 * Block management methods.
 *
 * @package Blockchain\Service
 */
class BlockManager
{
    public function calculateHash(Block $block): string {
        $record = (string)$block->index . $block->timestamp . (string)$block->bpm . $block->prevHash;
        $hashed = hash('sha256', $record);
        return $hashed;
    }
    
    public function generateBlock(Block $oldBlock, int $bpm): Block {
	    $newBlock = new Block();
	    
	    $newBlock->index = $oldBlock->index + 1;
	    $newBlock->timestamp = (string)time();
        $newBlock->bpm = $bpm;
        $newBlock->prevHash = $oldBlock->hash;
	    $newBlock->hash = $this->calculateHash($newBlock);

	    return $newBlock;
    }
    
    public function isBlockValid(Block $newBlock, Block $oldBlock): bool {
	    if (($oldBlock->index+1) != $newBlock->index) {
		    return false;
	    }

	    if ($oldBlock->hash != $newBlock->prevHash) {
		    return false;
	    }

	    if ($this->calculateHash($newBlock) != $newBlock->hash) {
		    return false;
	    }

	    return true;
    }
    
    public function getGenesisBlock(): Block
    {
        $genesisBlock = new Block();
        
	    $genesisBlock->index = 0;
	    $genesisBlock->timestamp = (string)time();
        $genesisBlock->bpm = 0;
        $genesisBlock->prevHash = "";
	    $genesisBlock->hash = $this->calculateHash($genesisBlock);
        
        return $genesisBlock;
    }
}
