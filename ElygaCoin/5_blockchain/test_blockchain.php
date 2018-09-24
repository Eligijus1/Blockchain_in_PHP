<?php
require_once('Pow.php');
require_once('Block.php');
require_once('Blockchain.php');

$b = new Blockchain('Genesis block');
$b->add('another block');
$b->add('yet another one');
print $b."\n\nIS VALID? ";


