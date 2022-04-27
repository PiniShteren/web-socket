<?php

require __DIR__ . "../../vendor/autoload.php";

require "../src/Chat.php";

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    $_ENV['PORT']
);

$server->run();