<?php

namespace Espresso\Http;

use Espresso\Cthreads\CThread;

class CCycleThread extends CThread
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
