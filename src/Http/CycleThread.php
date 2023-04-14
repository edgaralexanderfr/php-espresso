<?php

namespace Espresso\Http;

use Espresso\Gthreads\GThread;

class CycleThread extends GThread
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function run(): void
    {
        ($this->callable)();
    }
}
