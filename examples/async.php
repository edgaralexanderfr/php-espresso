<?php

require_once 'vendor/autoload.php';

use function Espresso\Event\async;
use Espresso\Http\Request;
use Espresso\Http\Response;
use Espresso\Http\Router;
use Espresso\Http\Server;

const SMALLER_FILE_PATH = __DIR__ . '/files/smaller-file.txt';
const BIGGER_FILE_PATH = __DIR__ . '/files/bigger-file.txt';

function read_file(string $path, int $bytes, callable $callable = null): void
{
    $file = fopen($path, 'r');
    $file_size = filesize($path);
    $content = '';
    $read_bytes = 0;

    async(function () use ($bytes, $callable, &$file, $file_size, &$content, &$read_bytes) {
        if ($read_bytes < $file_size) {
            $chunk_size = min($file_size - $read_bytes, $bytes);
            $chunk = fread($file, $chunk_size);
            $content .= $chunk;
            $read_bytes += $chunk_size;

            return false;
        }

        if ($callable) {
            $callable($content);
        }
    });
}

$server = new Server();
$router = new Router();

$router->get('/read-file', function (Request $request, Response $response, callable $next) {
    $size = $request->getParam('size');

    $file_path = $size == 'big' ? BIGGER_FILE_PATH : SMALLER_FILE_PATH;

    read_file($file_path, 8, function (string $content) use ($request, $response, $next, $size) {
        $response->send([
            'file_content' => $content,
            'size' => $size,
        ]);

        $next();
    });
});

$server->use($router);
$server->async(true);

$server->listen(80, function () use ($server) {
    $server->log('Listening at port 80...');
});
