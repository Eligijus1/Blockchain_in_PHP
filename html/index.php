<?php
/**
 * Invoke examples:
 * ================
 * curl -d "action=view" -X POST http://localhost:8000
 * curl -d "action=write&bpm=5" -X POST http://localhost:8000
 *
 */
include_once 'Model/Block.php';
include_once 'Service/BlockManager.php';
include_once 'Service/BlockchainManager.php';

use Blockchain\Model\Block;
use Blockchain\Service\BlockManager;
use Blockchain\Service\BlockchainManager;

// Define global variables:
$dataFilePath      = __DIR__ . '/Data/blockchain.data';
$action            = isset($_POST["action"]) ? $_POST["action"] : null;
$bpm               = isset($_POST["bpm"]) ? $_POST["bpm"] : null;
$blockManager      = new BlockManager();
$blockchainManager = new BlockchainManager($dataFilePath, $blockManager);

// Check if "action" defined:
if (empty($action)) {
    error_log("'action' not defined.");
    return;
}

// Check if valid action:
if ($action !== 'view' && $action !== 'write') {
    error_log("Unhandled action '{$action}' detected.");
    return;
}

// Handling 'view' action:
if ($action === 'view') {
    $blockchain = $blockchainManager->getBlockchain();
    
    echo "\nindex\ttimestamp\tbpm\thash\t\t\t\t\t\t\t\t\tprevHash";
    echo "\n" . str_repeat('-', 170);
    foreach($blockchain as $block) {
        echo "\n{$block->index}\t{$block->timestamp}\t{$block->bpm}\t{$block->hash}\t{$block->prevHash}";
    }
}

// Handling 'view' action:
if ($action === 'write') {
    // Check if "bpm" is numeric:
    if (!is_numeric($bpm)) {
        error_log("bpm value '{$bpm}' is not numeric.");
        return;
    }
    
    // Add specified "bpm":
    if ($blockchainManager->addBPM($bpm)) {
        echo "\nbpm value '{$bpm}' added blockchain.";
    }
    else {
        error_log("Failed bpm value '{$bpm}' adding to blockchain.");
        return;
    }
}


echo "\n";
