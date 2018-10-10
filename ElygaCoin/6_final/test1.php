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

$state = State::load('Eligijus');
//$stateEncoded = base64_encode(serialize($state));

// DEBUG:
//error_log("DEBUG: index invoked on port {$port} and user {$user}.");
//error_log("DEBUG: data received: {$dataReceived}");
//error_log("DEBUG: data decoded: {$dataDecoded}");
//error_log("DEBUG: will return state: " . print_r($state, true));
//error_log("DEBUG: will return state: " . $stateEncoded);
//error_log("DEBUG: will return state: " . base64_decode($stateEncoded));

//print $stateEncoded;
//$state2 = unserialize(base64_decode($stateEncoded));
//
//if ($state2 instanceof State) {
//    printf("\e[0;32m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Correct state.\e[0m\n");
//} else {
//    printf("\e[0;31m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Wrong state.\e[0m\n");
//}

$data = base64_encode(serialize($state));
$peerState = @file_get_contents('http://localhost:' . 8000 . '/gossip', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\n",
        'content' => $data
    ]
]));

//$peerStateDecoded = base64_decode(trim($peerState));
//print $peerState;

// Test 1:
//$peerState = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
//    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
//},$peerState );
//if (@unserialize($peerState) instanceof State) {
//    printf("\n\e[0;32m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Correct state.\e[0m\n");
//} else {
//    printf("\n\e[0;31m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Wrong state.\e[0m\n");
//}

// Test 2:
echo base64_decode($peerState);
$state = unserialize(base64_decode($peerState));
if ($state instanceof State) {
    printf("\n\e[0;32m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Correct state.\e[0m\n");
} else {
    printf("\n\e[0;31m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Wrong state.\e[0m\n");
}

echo "\n";
