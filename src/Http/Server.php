<?php

namespace Espresso\Http;

class Server
{
    const LINE_BREAK = "\r\n";

    /** @var resource TCP connection. */
    private $server;

    public function listen(int $port = 80, callable $callback = null)
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
}
