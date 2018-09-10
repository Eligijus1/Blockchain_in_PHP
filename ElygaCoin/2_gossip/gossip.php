<?php

require_once 'State.php';

$user = $argv[1];
$port = (int)$argv[2];
$peerPort = isset($argv[3]) ? (int)$argv[3] : null;
printf("Listening for %s on port %d\n", $user, $port);
if ($peerPort) {
    printf("Connecting to port %d\n", $peerPort);
}
(new State($user, $port, $peerPort))->loop();

