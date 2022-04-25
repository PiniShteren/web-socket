<?php

require __DIR__ . "../../vendor/autoload.php";

require __DIR__ . "../../src/chat.php";

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;


$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8282
);

$server->run();