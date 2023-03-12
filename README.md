# PHP Espresso Framework

[![View last release](https://img.shields.io/badge/version-v1.0.0-informational.svg)](https://github.com/edgaralexanderfr/php-espresso/releases/latest)
![Experimental](https://img.shields.io/badge/experimental-critical.svg)

**PHP Espresso** is a small PHP Framework I created to develop runtime web servers for PHP running CLI programs and scripts. Very similar to frameworks like **Express** for _**NodeJS**_, **Gorilla Mux** for _**Golang**_, etc.

**IMPORTANT NOTE:** This is just a _proof of concept_ to test the reliability of a runtime web server for PHP, its use and implementation is discouraged for **production-level** projects as it's an experimental framework for learning purposes.

PHP was designed to be a **Single-Threaded** **Non-Asynchronous** programming language, hence, the implementation of these type of web servers is very difficult as there will be always blocking processes for each request, hence, this server/framework is non-scalable.

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
$ curl http://localhost
{"message":"Hello world!","code":200}
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
