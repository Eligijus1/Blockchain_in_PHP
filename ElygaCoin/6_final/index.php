<?php

namespace ElygaCoinFinal;

require_once __DIR__ . '/State.php';
require_once __DIR__ . '/Gossip.php';
require_once __DIR__ . '/Key.php';
require_once __DIR__ . '/Pki.php';
require_once __DIR__ . '/Blockchain.php';
require_once __DIR__ . '/Block.php';
require_once __DIR__ . '/Pow.php';
require_once __DIR__ . '/Transaction.php';

if ('/gossip' == $_SERVER['PATH_INFO'] && 'POST' == $_SERVER['REQUEST_METHOD']) {
    $port = $_SERVER['SERVER_PORT'];
    $user = file_get_contents("data/{$port}.user");
    $dataReceived = file_get_contents('php://input');
    $dataDecoded = base64_decode($dataReceived);

    $state = new State($user, null);
    $state->reload();

    // DEBUG:
    /*
    error_log("DEBUG: index.php invoked on port {$port} and using user {$user}.");
    error_log("DEBUG: data received: {$dataReceived}");
    error_log("DEBUG: data decoded: {$dataDecoded}");
    */

    $state->update(unserialize($dataDecoded));
    print base64_encode(serialize($state));
}

