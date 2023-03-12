<?php

require_once 'vendor/autoload.php';

use Espresso\Http\Request;
use Espresso\Http\Response;
use Espresso\Http\Router;
use Espresso\Http\Server;

$server = new Server();
$router = new Router();

$router->post('/users', function (Request $request, Response $response) {
    $body_json = $request->getJSON();

    return $response->send([
        'message' => 'User created successfully',
        'code' => 201,
        'user' => $body_json,
    ], 201);
});

$server->use($router);

$server->listen(80, function () use ($server) {
    $server->log('Listening at port 80...');
});
