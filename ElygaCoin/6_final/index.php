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

    if ($stateReceived->blockChain && !$stateReceived->blockChain->isValid()) {
        error_log("\e[0;31mERROR: received state is wrong.\e[0m\n");
        return;
    }

    $state->update($stateReceived);
    $stateEncoded = base64_encode(serialize($state));

    print $stateEncoded;
}

if ('/transfer' == $_SERVER['PATH_INFO'] && 'POST' == $_SERVER['REQUEST_METHOD']) {
    $port = $_SERVER['SERVER_PORT'];
    $user = file_get_contents("data/{$port}.user");
    $state = State::load($user);
    $from = file_get_contents("data/" . $_POST['from'] . ".pub");
    $to = file_get_contents("data/" . $_POST['to'] . ".pub");
    $amount = (int)$_POST['amount'];
    $key = Key::load($user);
    $transaction = new Transaction($from, $to, $amount, $key->privateKey);
    $state->blockChain->add($transaction);

    if (!$state->blockChain->isValid()) {
        print("\e[0;31mERROR: New blockchain is not valid.\e[0m\n");
        return;
    }

    $state->save();
    $state->reload();

    print("\e[0;32mOK\e[0m\n");
}

