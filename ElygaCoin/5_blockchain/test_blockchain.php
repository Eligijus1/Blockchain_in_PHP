<?php
require_once('Pow.php');
require_once('Block.php');
require_once('Blockchain.php');

use ElygaCoin\Blockchain;

$b = new Blockchain('Genesis block');
$b->add('another block');
$b->add('yet another one');
print $b . "\n\nIS VALID? ";
var_export($b->isValid());
print "\n\n";

[$b->blocks[0], $b->blocks[1]] = [$b->blocks[1], $b->blocks[0]];
print $b . "\n\nIS VALID? ";
var_export($b->isValid());
print "\n\n";
