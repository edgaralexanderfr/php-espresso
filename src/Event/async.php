<?php

namespace Espresso\Event;

function async(callable $callable): void
{
    static $started_loop;

    Loop::schedule($callable);

    if (!$started_loop) {
        $started_loop = true;

        Loop::process();
    }
}
