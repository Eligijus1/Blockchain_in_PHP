<?php
require_once('Pow.php');

$message = 'Hello World';

$start = microtime(true);
$nonce = Pow::findNonce($message, '000');
print "Nonce: {$nonce}\nHash: " . hash('sha256',$message . $nonce) . "\nFinding nonce with 3 zero: " . (microtime(true) - $start) . " seconds\n";

$start = microtime(true);
$nonce = Pow::findNonce($message, '0000');
print "\nNonce: {$nonce}\nHash: " . hash('sha256',$message . $nonce) . "\nFinding nonce with 4 zero: " . (microtime(true) - $start) . " seconds\n";

$start = microtime(true);
$nonce = Pow::findNonce($message, '00000');
print "\nNonce: {$nonce}\nHash: " . hash('sha256',$message . $nonce) . "\nFinding nonce with 5 zero: " . (microtime(true) - $start) . " seconds\n";

$start = microtime(true);
$nonce = Pow::findNonce($message, '000000');
print "\nNonce: {$nonce}\nHash: " . hash('sha256',$message . $nonce) . "\nFinding nonce with 6 zero: " . (microtime(true) - $start) . " seconds\n";


