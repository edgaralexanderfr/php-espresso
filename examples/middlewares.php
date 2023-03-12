<?php

require_once 'vendor/autoload.php';

use Espresso\Http\Request;
use Espresso\Http\Response;
use Espresso\Http\Router;
use Espresso\Http\Server;

define('AUTH_CREDENTIALS', (object) [
    'user' => 'john.doe@example.com',
    'pass' => '1234567890', // Please... don't...
]);

/**
 * Middleware for admin authentication.
 */
function auth(Request $request, Response $response, callable $next)
{
    $authorization = $request->getHeader('Authorization') ?? '';
    $auth = explode(' ', $authorization);
    $type = $auth[0] ?? '';
    $token = $auth[1] ?? '';

    $credentials = explode(':', base64_decode($token));
    $user = $credentials[0] ?? null;
    $pass = $credentials[1] ?? null;

    if ($type != 'Bearer' || $user != AUTH_CREDENTIALS->user || $pass != AUTH_CREDENTIALS->pass) {
        return $response->send([
            'message' => Espresso\Http\CODES[401],
            'code' => 401,
        ], 401);
    }

    $next();
}

/** @var stdClass[] */
$users = [];
/** @var int */
$users_id = 1;

$server = new Server();
$router = new Router();

// Global middleware to check service status:
$server->use(function (Request $request, Response $response, callable $next) use ($argv) {
    $status = $argv[1] ?? '';

    if ($status == 'service-closed') {
        return $response->send([
            'message' => 'Service unavailable temporary due to maintenance',
            'code' => 503,
        ], 503);
    }

    $next();
});

$router->get('/users', function (Request $request, Response $response) use (&$users) {
    return $response->send($users);
});

$router->post('/users', 'auth', function (Request $request, Response $response) use (&$users, &$users_id) {
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

$server->use($router);

$server->listen(80, function () use ($server) {
    $server->log('Listening at port 80...');
});
