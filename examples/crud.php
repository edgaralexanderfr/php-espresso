<?php

require_once 'vendor/autoload.php';

use Espresso\Http\Request;
use Espresso\Http\Response;
use Espresso\Http\Router;
use Espresso\Http\Server;

/** @var stdClass[] */
$users = [];
/** @var int */
$users_id = 1;

$server = new Server();
$router = new Router();

$router->get('/users', function (Request $request, Response $response) use (&$users) {
    return $response->send($users);
});

$router->get('/users/:id', function (Request $request, Response $response) use (&$users) {
    $id = $request->getId();

    foreach ($users as $user) {
        if (isset($user->{'id'}) && $user->id == $id) {
            return $response->send($user);
        }
    }

    return $response->send([
        'message' => 'User not found',
        'code' => 404,
    ], 404);
});

$router->post('/users', function (Request $request, Response $response) use (&$users, &$users_id) {
    $body = $request->getJSON();

    $email = $body->email ?? null;
    $name = $body->name ?? null;

    if (!$email || !$name) {
        return $response->send([
            'message' => 'Email and Name are required',
            'code' => 400,
        ], 400);
    }

    $user = (object) [
        'id' => $users_id++,
        'email' => $email,
        'name' => $name,
    ];

    $users[] = $user;

    return $response->send([
        'message' => 'User created successfully',
        'code' => 201,
        'user' => $user,
    ], 201);
});

$router->patch('/users/:id', function (Request $request, Response $response) use (&$users) {
    $id = $request->getId();
    $body = $request->getJSON();

    foreach ($users as &$user) {
        if (isset($user->{'id'}) && $user->id == $id) {
            $user->email = $body->email ?? $user->email;
            $user->name = $body->name ?? $user->name;

            return $response->send([
                'message' => 'User updated successfully',
                'code' => 200,
                'user' => $user,
            ]);
        }
    }

    return $response->send([
        'message' => 'User not found',
        'code' => 404,
    ], 404);
});

$router->delete('/users/:id', function (Request $request, Response $response) use (&$users) {
    $id = $request->getId();

    foreach ($users as $i => &$user) {
        if (isset($user->{'id'}) && $user->id == $id) {
            array_splice($users, $i, 1);

            return $response->send([
                'message' => 'User deleted successfully',
                'code' => 200,
            ]);
        }
    }

    return $response->send([
        'message' => 'User not found',
        'code' => 404,
    ], 404);
});

$server->use($router);

$server->listen(80, function () use ($server) {
    $server->log('Listening at port 80...');
});
