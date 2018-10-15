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
    $user = file_get_contents("data/{$port}.user");
    $state = State::load($user);
    $from = $_POST['from'];
    $to = $_POST['to'];
    $amount = (int)$_POST['amount'];
    $key = Key::load($user);
    $transaction = new Transaction($from, $to, $amount, $key->privateKey);
    $state->blockChain->add($transaction);

    $state2 = State::load($user);
    $state2->update($state);

    print 'OK';
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
        printf("\nfrom " . substr($block->transaction->from, 72, 7) . " to " . substr($block->transaction->to, 72, 7) . " " . $block->transaction->amount);
    }

    print_r($state->peers);


    printf("\n");
}
