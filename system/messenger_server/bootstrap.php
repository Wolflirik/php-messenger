<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use system\messenger_server\Messenger;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use system\container\Container;

try{
    $container = new Container();
    $services = require_once __DIR__ . '/../config/ws_service.php';

    foreach($services as $service)
    {
        $provider = new $service($container);
        $provider->init();
    }

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Messenger($container)
            )
        ),
        8391
    );

    $server->run();
}
catch(\Exception $e)
{
    exit($e->getMessage());
}
