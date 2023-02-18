<?php

namespace Espresso\Http;

class Server
{
    public function listen(int $port = 80, callable $callback = null)
    {
        if ($callback) {
            $callback();
        }
    }
}
