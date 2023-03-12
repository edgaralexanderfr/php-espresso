# PHP Espresso Framework

[![View last release](https://img.shields.io/badge/version-v1.0.0-informational.svg)](https://github.com/edgaralexanderfr/php-espresso/releases/latest)
[![View last release](https://img.shields.io/badge/php->=8.0.0-informational.svg)](https://www.php.net/releases/8.0/es.php)
![Experimental](https://img.shields.io/badge/experimental-critical.svg)

**PHP Espresso** is a small PHP Framework I created to develop runtime web servers for PHP running CLI programs and scripts. Very similar to frameworks like **Express** for _**NodeJS**_, **Gorilla Mux** for _**Golang**_, etc.

**IMPORTANT NOTE:** This is just a _proof of concept_ to test the reliability of a runtime web server for PHP, its use and implementation is discouraged for **production-level** projects as it's an experimental framework for learning purposes.

PHP was designed to be a **Single-Threaded** **Non-Asynchronous** programming language, hence, the implementation of these type of web servers is very difficult as there will be always blocking processes for each request, hence, this server/framework is non-scalable.

## Requirements

1. **PHP 8.0.0 or major**
2. **Have PHP sockets module installed and enabled**
3. **Composer**
4. **Have a initted Composer project**

## Installation

Install **PHP Espresso** via Composer:

```bash
composer require edgaralexanderfr/php-espresso
```

## Usage

### Creating a basic web server

Create a _**server.php**_ file inside your project with the following program:

```php
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
```

Run the server:

```bash
php server.php # Use sudo if necessary for port 80
```

Visit http://localhost or execute:

```bash
curl http://localhost
```

And voila! 🎉

### Serving a basic static HTML Page

```php
<?php

require_once 'vendor/autoload.php';

use Espresso\Http\Request;
use Espresso\Http\Response;
use Espresso\Http\Router;
use Espresso\Http\Server;

$server = new Server();
$router = new Router();

$router->get('/php-espresso-page', function (Request $request, Response $response) {
    return $response->setPayload(
        <<<HTML
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <title>My Web Page with PHP Espresso!</title>
                </head>
                <body>
                    <h1>My Web Page with PHP Espresso!</h1>

                    <p>This page was served using PHP Espresso.</p>
                </body>
            </html>
        HTML
    );
});

$server->use($router);

$server->listen(80, function () use ($server) {
    $server->log('Listening at port 80...');
});
```

Visit http://localhost/php-espresso-page in your browser.

### Create a POST request:

```php
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
```

Execute a POST request:

```bash
curl -X POST http://localhost/users -d '{"name":"Alexander The Great"}'
```

### Complete Rest API CRUD example:

```php
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
```

Create a couple of users:

```bash
curl -X POST http://localhost/users -d '{"email":"john.doe@example.com","name":"John Doe"}'
curl -X POST http://localhost/users -d '{"email":"jane.doe@example.com","name":"Jane Doe"}'
```

Retrieve all created users:

```bash
curl http://localhost/users
```

Retrieve user with `id` 2:

```bash
curl http://localhost/users/2
```

Update user with `id` 1:

```bash
curl -X PATCH http://localhost/users/1 -d '{"name":"John James Doe"}'
```

Delete user with `id` 2:

```bash
curl -X DELETE http://localhost/users/2
```

### Defining middlewares

**PHP Espresso** supports global and route middlewares. You can assign as much middlewares to a single route as you want.

To do so, you can create a new _middlewares.php_ file and add the following code:

```php
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
```

If you run:

```bash
php middlewares.php service-closed
```

And do:

```bash
curl http://localhost/users
```

Or:

```bash
curl -X POST http://localhost/users -d '{"email":"john.doe@example.com","name":"John Doe"}'
```

You will get the following message:

```bash
{"message":"Service unavailable temporary due to maintenance","code":503}
```

If you kill the previous server with <kbd>CTRL</kbd>+<kbd>C</kbd> and then run:

```bash
php middlewares.php
```

You will be able to retrieve the users list now, e.g:

```bash
curl http://localhost/users
```

To create a new user you need to be authenticated, to do so, assign an encoded **Bearer Token** using `base64` to a variable and then pass the `Authorization Header` to `curl` command:

```bash
AUTH_TOKEN=$(echo 'john.doe@example.com:1234567890' | base64)
```

```bash
curl -X POST http://localhost/users -d '{"email":"john.doe@example.com","name":"John Doe"}' -H "Authorization: Bearer ${AUTH_TOKEN}"
```
