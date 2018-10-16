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
        'ignore_errors' => 1,
        'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\nAccept-Charset: ISO-8859-1",
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
//$peerStateHardcoded = 'TzoyMDoiRWx5Z2FDb2luRmluYWxcU3RhdGUiOjM6e3M6NDoibmFtZSI7czo4OiJFbGlnaWp1cyI7czo1OiJwZWVycyI7YToxOntpOjgwMDA7YjoxO31zOjEwOiJibG9ja0NoYWluIjtPOjI1OiJFbHlnYUNvaW5GaW5hbFxCbG9ja2NoYWluIjoxOntzOjY6ImJsb2NrcyI7YToxOntpOjA7TzoyMDoiRWx5Z2FDb2luRmluYWxcQmxvY2siOjQ6e3M6MTI6InByZXZpb3VzSGFzaCI7TjtzOjU6Im5vbmNlIjtpOjExNzAwNjU5O3M6NDoiaGFzaCI7czo2NDoiMDAwMDAwNWE1ZWYxODhmOTA5Njg5MDMyMDkwYWU3NGYyMjE1Y2MxZjNiNmM2OGZlMDMyYzJkYzIyZjA0MWE4MiI7czoxMToidHJhbnNhY3Rpb24iO086MjY6IkVseWdhQ29pbkZpbmFsXFRyYW5zYWN0aW9uIjo0OntzOjQ6ImZyb20iO047czoyOiJ0byI7czo0NTE6Ii0tLS0tQkVHSU4gUFVCTElDIEtFWS0tLS0tCk1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBMVVDS2RWMmVlM3NISWZqZEw5OGgKRllGYzZyeHUzWE92WDRrNUoyaEpzOHNnTVlKRFZMR0NtVFFtUkw0SkcveDJ0VDdTd1Bhc0owNitQUjVacVJWMQpRRHFlRzlraXJqTGNsREpqTXhianU5ZFBNMVcxWXFwREUrTkY3WVlzU2x1WDRBSmFxTG9OZUR4V3d2OCtlYkVnCm1hOUdiOW5RNjUvQzFOYzM2enFUaU9KaXBMV3lVbzZISEs2ZHpVeXhtN090OEwySUVBR3R0Q1gvMmNidE9EV0IKTHJTem5kTlBzaXduZXExeWVKRHZadnZ6d1hQRFJtd0hQTkRzYUk2YzdzYWlibEFFdmJWc0JqdXJ6bE1PSWt4VQpiWDRWcDZ3NkNrUTRSck9nZE1GZjc4UTgyRUMzemRLcXZlYnVNK2xncDU2dUxyVzdsYVVvQ0dmSHVyY3lYY3VUCnRRSURBUUFCCi0tLS0tRU5EIFBVQkxJQyBLRVktLS0tLQoiO3M6NjoiYW1vdW50IjtpOjEwMDAwMDA7czo5OiJzaWduYXR1cmUiO3M6MzQ0OiJpR3k1TXkxemEzQTcyaVpiUm9mSEg5ZzJONVpsbUtvMGpwQmpzMFBNaEwzNG1Bc2N1ZGoxckV6RlFKTlhHaGRRNW1RckpKckM3UGx2S0dMRWlkV2hkbXVhWnByVUUwbUQ5ZWFvekFXZzZFNU04ZDJGdWZHenlhYk5HYXNVT1hCZUQ0VkZxdlFJOHM0V2J2dXlRWWRxOVFiZGR4RDhCdHFNZUYvcDhXWWtpRVRldGxabE5EY3ZUSlZLd0JlTTRtUHF5a2tkK2hmTVpuRUpMVm5SOE1IS0hBUUlFZzVYNkExMXBWUk9SdC9pUnJFcXcydzN6MnovZ2xjajJQVmxDMXJ3clRzZVVRd3dGSjFPRERoZzlXWVVpQnkwYVduQUVSZklMZGZUbEdPSkZKTGtoTWxRUXhGdzN0UEtJMG5GM0VKYnFMVHV5dUFEeVNLMm5KK2hYWUtPZ0E9PSI7fX19fX0=';
//echo "\n{$peerState}\n";
//echo base64_decode($peerStateHardcoded);
//echo base64_decode($peerState);
//echo \mb_detect_encoding($peerState, "auto");
//$peerState = str_replace("\r", "", $peerState);
//$peerState = str_replace("\n", "", $peerState);
//$peerState = str_replace(chr(27), "", $peerState);
//$peerState = str_replace(chr(91), "", $peerState);
//$peerState = str_replace(chr(48), "", $peerState);
//$peerState = str_replace(chr(59), "", $peerState);
//$peerState = str_replace(chr(51), "", $peerState);
//$peerState = preg_replace('~[\W\s]~', '', $peerState);
//$peerState = print_r($peerState, true);
//print "\n{$peerState}";
//$peerState = html_entity_decode($peerState);
//$peerState = utf8_decode($peerState);
//$peerState = iconv("UTF-8", "ISO-8859-1//IGNORE", $peerState);
//$peerState = iconv("UTF-8", "ISO-8859-1", $peerState);
//$peerState = utf8_decode(htmlspecialchars(iconv(mb_internal_encoding(), 'utf-8', $peerState)) );
//$peerState = preg_replace('/[^\x{20}-\x{7F}]/u','', $peerState);
//$peerState = preg_replace('/[^\x{00}-\x{7F}]/u','', $peerState);
//echo "\n|{$peerState}|\n";
//if ($peerState == $peerStateHardcoded) {
//    echo "\nEqual\n";
//} else {
//    //echo "\n{$peerState}\n\n{$peerState}\n";
//    //print_r(trim($peerState));
//    //echo trim(preg_replace('/\s+/', ' ', $peerState));
//    //echo base64_decode(trim(preg_replace('/\s+/', ' ', $peerState)));
//    //echo base64_decode(str_replace(array("\n", "\r"), ' ', $peerState));
//    //echo base64_decode(preg_replace('/\R+/', " ", $peerState));
//    echo "\nNot Equal\n";
//    echo strlen($peerState) . "=" . strlen($peerStateHardcoded) . "\n";
//    $peerStateChars = str_split($peerState);
//    $peerStateHardcodedChars = str_split($peerStateHardcoded);
//    $i = 0;
//    foreach ($peerStateChars as $peerStateChar) {
//        if (!empty($peerStateHardcodedChars[$i]) && $peerStateChars[$i] != $peerStateHardcodedChars[$i]) {
//            echo "{$i} not equal: {$peerStateChars[$i]} (char " . ord($peerStateChar) . ") != {$peerStateHardcodedChars[$i]}\n";
//
//            return;
//        }
//
//        $i++;
//    }
//}
//echo base64_decode($peerState);
//print_r($peerState, true);

// Final test:
echo "\n";
$peerStateBase64Decoded = base64_decode($peerState);
echo $peerStateBase64Decoded;
//$state = unserialize(base64_decode($peerState),true);
//if ($state instanceof State) {
//    printf("\n\e[0;32m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Correct state.\e[0m\n");
//} else {
//    printf("\n\e[0;31m" . date_format(new \DateTime(), 'Y.m.d H:i:s') . " Wrong state.\e[0m\n");
//}

// Add new line at end:
echo "\n";
