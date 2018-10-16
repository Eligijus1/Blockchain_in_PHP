<?php

namespace ElygaCoinFinal;

//header('Content-Type: text/html; charset=ISO-8859-1');

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

    print $stateEncoded;
}

if ('/transfer' == $_SERVER['PATH_INFO'] && 'POST' == $_SERVER['REQUEST_METHOD']) {
    $port = $_SERVER['SERVER_PORT'];
    $portTo = file_get_contents("data/" . $_POST['toName'] . ".port");
    $user = file_get_contents("data/{$port}.user");
    $state = State::load($user);
    $from = $_POST['from'];
    $to = $_POST['to'];
    $from = file_get_contents("data/" . $_POST['fromName'] . ".pub");
    $to = file_get_contents("data/" . $_POST['toName'] . ".pub");
    $amount = (int)$_POST['amount'];
    $key = Key::load($user);
    $transaction = new Transaction($from, $to, $amount, $key->privateKey);
    $state->blockChain->add($transaction);

//    print($to);
//    print(file_get_contents("data/Jonas.pub"));

    if (!$state->blockChain->isValid()) {
        print("\e[0;31mERROR: New blockchain is not valid.\e[0m\n");
        //return;
    }

    //v1:
    //$state2 = State::load($user);
    //$state2->update($state);

    //v2:
    //$state->save();
    //$state->reload();

    //v3:
    $data = base64_encode(serialize($state));
    $peerState = @file_get_contents('http://localhost:' . $portTo . '/gossip', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-type: application/json\r\nContent-length: " . strlen($data) . "\r\n",
            'content' => $data
        ]
    ]));

    if ($peerState) {
        print("\e[0;32mOK\e[0m\n");
    } else {
        print("\e[0;31mERROR: Peer state is empty.\e[0m\n");
    }
}

if ('/balances' == $_SERVER['PATH_INFO'] && 'GET' == $_SERVER['REQUEST_METHOD']) {
    $port = $_SERVER['SERVER_PORT'];
    $user = file_get_contents("data/{$port}.user");
    $state = State::load($user);

    printf("Balances: \n%s", $state->blockChain->balancesAsString());
    printf("\n");
}

if ('/blocks' == $_SERVER['PATH_INFO'] && 'GET' == $_SERVER['REQUEST_METHOD']) {
    $port = $_SERVER['SERVER_PORT'];
    $user = file_get_contents("data/{$port}.user");
    $state = State::load($user);

    $blocks = $state->blockChain->blocks;

    printf("Blocks:\n");
    foreach ($blocks as $block) {
        printf("\nfrom " . substr($block->transaction->from, 72, 7) . " to " . substr($block->transaction->to, 72,
                7) . " " . $block->transaction->amount);
    }

    print_r($state->peers);


    printf("\n");
}
