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
