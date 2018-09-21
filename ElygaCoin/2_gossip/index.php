<?php

require_once __DIR__ . '/State.php';

//DEBUG:
// error_log("Server path: {$_SERVER['PATH_INFO']}; Server port: {$_SERVER['SERVER_PORT']}; Request method: {$_SERVER['REQUEST_METHOD']}");

if ('/gossip' == $_SERVER['PATH_INFO'] && 'POST' == $_SERVER['REQUEST_METHOD']) {
    $port = $_SERVER['SERVER_PORT'];
    $user = file_get_contents("data/{$port}.user");

    //error_log("DEBUG 1: Server path: {$_SERVER['PATH_INFO']}; Server port: {$port}; Server user: {$user}");

    $state = new State($user);
    $state->update(json_decode(file_get_contents('php://input'), true));
    print json_encode($state->state);
}

