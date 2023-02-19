<?php

namespace Espresso\Http;

use stdClass;

class Server
{
    const LINE_BREAK = "\r\n";

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

            $http = $this->handleRequest($client);

            $body = "<html>
                    <head>
                        <title>PHP Espresso</title>
                    </head>
                    </body>
                        <h1>PHP Espresso</h1>
                        <p>Content served with PHP Espresso.</p>
                    </body>
                </html>";

            $content_length = strlen($body);

            $response = 'HTTP/1.1 200 OK' . self::LINE_BREAK;
            $response .= 'Server: PHP Espresso' . self::LINE_BREAK;
            $response .= "Content-Length: $content_length" . self::LINE_BREAK;
            $response .= 'Content-Type: text/html' . self::LINE_BREAK;
            $response .= 'Connection: Closed' . self::LINE_BREAK;
            $response .= '' . self::LINE_BREAK;
            $response .= $body . self::LINE_BREAK;

            fwrite($client, $response);
            fclose($client);
        }
    }

    /**
     * @param mixed $client Client of type `resource|Socket` to handle.
     */
    private function handleRequest(&$client): stdClass
    {
        $router = null;
        $request = new Request();
        $payload = '';
        $read_payload = false;
        $l = 1;

        /** @todo Fix this block... */
        while (($line = trim(fgets($client))) != '' && !$read_payload) {
            if ($l == 1) {
                $http_header = explode(' ', $line);
                $router = $this->getRequestRouter($http_header[1], $http_header[0]);
            } else {
                if ($line == '') {
                    if ($read_payload) {
                        break;
                    } else {
                        $read_payload = true;
                    }
                }

                if ($read_payload) {
                    $payload .= $line;
                } else {
                    $request->setHeader($line);
                }
            }

            $l++;
        }

        $request->setPayload($payload);

        return (object) [
            'router' => $router,
            'request' => $request,
        ];
    }

    private function getRequestRouter(string $resource, string $method = 'get'): ?Router
    {
        foreach ($this->routers as $router) {
            if ($router = $router->getRoute($resource, $method)) {
                return $router;
            }
        }

        return null;
    }
}
