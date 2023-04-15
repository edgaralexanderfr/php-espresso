<?php

namespace Espresso\Http;

use stdClass;
use const Espresso\Http\SOCKET_TIMEOUT;
use function Espresso\Event\async;

class Server
{
    public const MODE_SYNC = 0x00;
    public const MODE_ASYNC = 0x01;
    public const MODE_GTHREADS = 0x02;

    /** @var resource TCP connection. */
    private $server;

    /** @var array Array of type `Router[]` */
    private array $routers = [];

    /** @var array Array of type `callable[]` */
    private array $middlewares = [];

    /** @var int Indicates the mode in which the server is executing the requests processes. */
    private int $mode = self::MODE_SYNC;

    /** @var float Connection wait time for new client. Defaults to `SOCKET_TIMEOUT`. */
    private ?float $socket_timeout = SOCKET_TIMEOUT;

    /**
     * @param Router|callable $resource Router or middleware to use.
     */
    public function use(Router|callable $resource): void
    {
        if ($resource instanceof Router) {
            $this->routers[] = $resource;
        } else {
            $this->middlewares[] = $resource;
        }
    }

    public function async(bool $async): void
    {
        $this->mode = $async ? self::MODE_ASYNC : self::MODE_SYNC;
    }

    public function setMode(int $mode): void
    {
        $this->mode = $mode;
    }

    public function setSocketTimeout(?float $socket_timeout): void
    {
        $this->socket_timeout = $socket_timeout;
    }

    public function listen(int $port = 80, callable $callback = null): void
    {
        if ($this->mode == self::MODE_ASYNC) {
            $this->listenAsync($port, $callback);

            return;
        }

        if ($this->mode == self::MODE_GTHREADS) {
            $this->listenGThreads($port, $callback);
        }

        $error_code = 0;
        $error_message = null;

        $this->server = stream_socket_server("tcp://0.0.0.0:$port", $error_code, $error_message);

        if ($callback) {
            $callback();
        }

        while (true) {
            $client = @stream_socket_accept($this->server);

            if (!$client) {
                continue;
            }

            $this->handleCycle($client);
        }
    }

    public function listenAsync(int $port = 80, callable $callback = null): void
    {
        async(function () use ($port, $callback) {
            $error_code = 0;
            $error_message = null;

            $this->server = stream_socket_server("tcp://0.0.0.0:$port", $error_code, $error_message);

            async(function () {
                $client = @stream_socket_accept($this->server, $this->socket_timeout);

                if (!$client) {
                    return false;
                }

                $this->handleCycle($client);

                return false;
            });

            if ($callback) {
                $callback();
            }
        });
    }

    public function listenGThreads(int $port = 80, callable $callback = null): void
    {
        $error_code = 0;
        $error_message = null;

        $this->server = stream_socket_server("tcp://0.0.0.0:$port", $error_code, $error_message);

        if ($callback) {
            $callback();
        }

        while (true) {
            $client = @stream_socket_accept($this->server);

            if (!$client) {
                continue;
            }

            $cycle_thread = new CycleThread(function () use (&$client) {
                $this->handleCycle($client);
            });

            $cycle_thread->start();
        }
    }

    public function log($log): void
    {
        echo print_r($log, true) . PHP_EOL;
    }

    /**
     * @param mixed $client Client of type `resource` to handle.
     */
    private function handleCycle(&$client): void
    {
        $http = $this->buildRequest($client);

        $done = function () use ($client, $http) {
            $response = $this->buildResponse($http->response);

            fwrite($client, $response);
            fclose($client);
        };

        if (!$http->route || !$http->route->routes) {
            $http->response->setStatusCode(404);

            $done();

            return;
        }

        $routes = array_merge($this->middlewares, $http->route->routes);
        $route_index = -1;

        $next = function () use ($http, &$done, $routes, &$route_index, &$next) {
            $route_index++;

            if (!isset($routes[$route_index])) {
                $done();

                return;
            }

            $responded = $routes[$route_index]($http->request, $http->response, $next, $done, $http->route->id);

            if ($responded) {
                $done();
            }
        };

        $next();
    }

    /**
     * @param mixed $client Client of type `resource` to handle.
     */
    private function buildRequest(&$client): stdClass
    {
        $route = null;
        $request = new Request();
        $response = new Response();
        $payload = '';
        $l = 1;

        while (($line = trim(fgets($client))) != '') {
            if ($l == 1) {
                $http_header = explode(' ', $line);
                $route = $this->getRequestRouter($http_header[1], $http_header[0]);

                if ($route) {
                    $request->setId($route->id);
                    $request->setQueryString($route->query_string);
                }

                $this->log($line);
            } else {
                $request->setHeader($line);
            }

            $l++;
        }

        $bytes_to_read = socket_get_status($client)['unread_bytes'];

        if ($bytes_to_read > 0) {
            $payload = fread($client, $bytes_to_read);
        }

        $request->setPayload($payload);

        return (object) [
            'route' => $route,
            'request' => $request,
            'response' => $response,
        ];
    }

    private function buildResponse(Response $response): string
    {
        if (!$response->getHeader('Server')) {
            $response->setHeader('Server', 'PHP Espresso');
        }

        if (!$response->getHeader('Content-Length')) {
            $response->setHeader('Content-Length', strlen($response->getPayload()));
        }

        if (!$response->getHeader('Content-Type')) {
            $response->setHeader('Content-Type', 'text/html');
        }

        if (!$response->getHeader('Connection')) {
            $response->setHeader('Connection', 'Closed');
        }

        $response_packet = 'HTTP/1.1 ' . $response->getStatus() . LINE_BREAK;
        $response_packet .= implode(LINE_BREAK, array_values($response->getHeaders())) . LINE_BREAK;
        $response_packet .= '' . LINE_BREAK;
        $response_packet .= $response->getPayload();

        return $response_packet;
    }

    private function getRequestRouter(string $resource, string $method = 'get'): ?stdClass
    {
        foreach ($this->routers as $router) {
            if ($route = $router->getRoute($resource, $method)) {
                return $route;
            }
        }

        return null;
    }
}
