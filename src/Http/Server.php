<?php

namespace Espresso\Http;

use stdClass;

class Server
{
    /** @var resource TCP connection. */
    private $server;

    /** @var array Array of type `Router[]` */
    private array $routers = [];

    public function use(Router $router): void
    {
        $this->routers[] = $router;
    }

    public function listen(int $port = 80, callable $callback = null): void
    {
        $error_code = 0;
        $error_message = null;

        $this->server = stream_socket_server("tcp://127.0.0.1:$port", $error_code, $error_message);

        if ($callback) {
            $callback();
        }

        while (true) {
            $client = @stream_socket_accept($this->server);

            if (!$client) {
                continue;
            }

            $http = $this->buildRequest($client);

            if ($http->route?->route) {
                ((array) $http->route)['route']($http->request, $http->response, $http->route->id);
            } else {
                $http->response->setStatusCode(404);
            }

            $response = $this->buildResponse($http->response);

            fwrite($client, $response);
            fclose($client);
        }
    }

    public function log($log): void
    {
        echo print_r($log, true) . PHP_EOL;
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
