<?php
require_once('Pki.php');

[$privKey, $pubKey] = Pki::generateKeyPair();

print "Public key: \n{$pubKey}\n";
print "Private key: \n{$privKey}\n";

$message = 'Hello World!';
$encryptedMessage = Pki::encrypt($message, $privKey);
print "Encrypted message: {$encryptedMessage}\n";

$decryptedMessage = Pki::decrypt($encryptedMessage, $pubKey);
print "Decrypted message: {$decryptedMessage}\n";

var_dump(Pki::isValid($message, $encryptedMessage, $pubKey));

