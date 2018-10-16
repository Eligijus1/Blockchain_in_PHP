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

$state = State::load('Petras');

$data = base64_encode(serialize($state));
$peerState = @file_get_contents('http://localhost:' . 8000 . '/gossip', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'ignore_errors' => 1,
        'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\nAccept-Charset: ISO-8859-1",
        'content' => $data
    ]
]));

echo "\n";
$peerStateBase64Decoded = base64_decode($peerState);
echo $peerStateBase64Decoded;
$state2 = unserialize($peerStateBase64Decoded);
if ($state2 instanceof State) {
    printf("\n\e[0;32m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Correct state.\e[0m\n");
} else {
    printf("\n\e[0;31m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Wrong state.\e[0m\n");
}

// Add new line at end:
echo "\n";
