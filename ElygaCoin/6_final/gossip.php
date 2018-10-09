<?php

namespace ElygaCoinFinal;

require_once 'State.php';
require_once 'Gossip.php';
require_once 'Key.php';
require_once 'Pki.php';
require_once 'Blockchain.php';
require_once 'Block.php';
require_once 'Pow.php';
require_once 'Transaction.php';

$user = $argv[1];
$port = (int)$argv[2];
$peerPort = isset($argv[3]) ? (int)$argv[3] : null;
printf("Listening for %s on port %d\n", $user, $port);
if ($peerPort) {
    printf("Connecting to port %d\n", $peerPort);
}
(new Gossip($user, $port, $peerPort))->loop();

