<?php

require_once 'vendor/autoload.php';

use Espresso\Http\Request;
use Espresso\Http\Response;
use Espresso\Http\Router;
use Espresso\Http\Server;

const PORT = 80;

$server = new Server();
$router = new Router();

$router->get('/', function (Request $request, Response $response) {
    return $response->send([
        'message' => 'Hello world!',
        'code' => 200,
    ]);
});

$server->use($router);

$server->listen(PORT, function () use ($server) {
    $server->log('Listening at port ' . PORT . '...');
});
