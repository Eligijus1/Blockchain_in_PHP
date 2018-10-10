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
    /** @var State $stateReceived */
    $stateReceived = unserialize($dataDecoded);

    $state = State::load($user);
    $state->update($stateReceived);
    $stateEncoded = base64_encode(serialize($state));

    // DEBUG:
    //error_log("DEBUG: index invoked on port {$port} and user {$user}.");
    //error_log("DEBUG: data received: {$dataReceived}");
    //error_log("DEBUG: data decoded: {$dataDecoded}");
    //error_log("DEBUG: will return state: " . print_r($state, true));
    //error_log("DEBUG: will return state: " . $stateEncoded);
    //error_log("DEBUG: will return state: " . base64_decode($stateEncoded));
    //error_log("DEBUG: will return state: " . base64_decode($stateEncoded));

    header('Content-Type: text/html; charset=utf-8');
    echo $stateEncoded;
    //print base64_decode($stateEncoded);
    //echo eval(gzdeflate($stateEncoded, strlen($stateEncoded)));
    //echo $stateEncoded;
    //echo serialize($state);
}

