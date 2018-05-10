<?php
include_once 'Model/Block.php';
include_once 'Service/BlockManager.php';

use Blockchain\Model\Block;
use Blockchain\Service\BlockManager;

// Get important variables:
$dataFilePath = __DIR__ . '/Data/blockchain.data';
$action       = $_POST["action"];
$bpm          = $_POST["bpm"];
$blockManager = new BlockManager();
$block        = new Block();
$blockChain   = file_exists($dataFilePath) ? unserialize(file_get_contents($dataFilePath)) : [$blockManager->getGenesisBlock()];

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

// Check if "bpm" is numeric:
if (!is_numeric($bpm)) {
    error_log("bpm value '{$bpm}' is not numeric.");
    return;
}

// Print variables (DEBUG):
echo "\naction={$action}";
echo "\nbpm={$bpm}";
echo "\nBlocks count: " . count($blockChain);
echo "\n";

// Save blockchain:
file_put_contents($dataFilePath, serialize($blockChain));
