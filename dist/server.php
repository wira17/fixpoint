<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require dirname(__FILE__) . '/vendor/autoload.php';
require 'MyChat.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

echo "MyChat initialized...\n";

// Ganti port jadi 8081
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new MyChat()
        )
    ),
    8081
);

echo "WebSocket server started on port 8081...\n";

$server->run();
