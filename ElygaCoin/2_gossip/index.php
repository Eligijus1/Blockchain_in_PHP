<?php

require_once __DIR__ . '/State.php';

if ('/gossip' == $_SERVER['PATH_INFO'] && 'POST' == $_SERVER['REQUEST_METHOD']) {
    $port = $_SERVER['SERVER_PORT'];
    $user = file_get_contents("data/{$port}.user");
    $jsonReceived = file_get_contents('php://input');

    $state = new State($user);
    $state->update(json_decode($jsonReceived, true));
    print json_encode($state->state);
}

