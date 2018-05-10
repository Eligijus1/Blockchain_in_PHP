<?php

namespace Blockchain\Service;

use Blockchain\Model\Block;
use Blockchain\Service\BlockManager;

/**
 * Class BlockchainManager
 *
 * Blockchain management methods.
 *
 * @package Blockchain\Service
 */
class BlockchainManager
{
    /** @var string */
    private $dataFilePath;
    
    /** @var BlockManager */
    private $blockManager;
    
    public function __construct(string $dataFilePath, BlockManager $blockManager)
    {
        $this->dataFilePath = $dataFilePath;
        $this->blockManager = $blockManager;
    }
    
    /**
     * @param int $bpm
     *
     * @return bool
     */
    public function addBPM(int $bpm): bool
    {
        $blockchain = $this->readBlockchain();
    
        $oldBlock = $blockchain[count($blockchain)-1];
        $newBlock = $this->blockManager->generateBlock($oldBlock, $bpm);
        
        if (empty($newBlock)) {
            return false;
        }
        
        if (!$this->blockManager->isBlockValid($newBlock, $oldBlock)) {
            return false;
        }
        
        $newBlocks = $this->readBlockchain();
        $newBlocks[] = $newBlock;
        
        $this->replaceChain($newBlocks);
        
        return true;
    }

    /**
     * @param Block[] $newBlocks
     *
     * @return bool
     */
    public function replaceChain(array $newBlocks): bool {
        $blockchain = $this->readBlockchain();
	    if (count($newBlocks) > count($blockchain)) {
		    $this->saveBlockchain($newBlocks);
		    return true;
	    }

        return false;
    }
    
    /**
     * @return Block[]
     */
    public function getBlockchain(): array
    {
        return $this->readBlockchain();
    }

    /**
     * @param Block[] $blockchain
     */
    private function saveBlockchain(array $blockchain): void {
        file_put_contents($this->dataFilePath, serialize($blockchain));
    }
    
    /**
     * @return Block[]
     */
    private function readBlockchain(): array {
        return (file_exists($this->dataFilePath) ? unserialize(file_get_contents($this->dataFilePath)) : [$this->blockManager->getGenesisBlock()]);
    }
}
