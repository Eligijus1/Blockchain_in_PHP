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
        $hashed = hash('sha256', $_POST['ppasscode']);
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
}
