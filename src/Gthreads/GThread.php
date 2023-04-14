<?php

namespace Espresso\Gthreads;

abstract class GThread
{
    private static ?\FFI $gthreads = null;

    public abstract function run(): void;

    public function start(): void
    {
        $ffi = self::getFFI();

        $ffi->GT_Exec(function () {
            $this->run();
        });
    }

    private static function getFFI(): \FFI
    {
        if (self::$gthreads) {
            return self::$gthreads;
        }

        self::$gthreads = \FFI::cdef(
            'int GT_Exec(void (*callable)());',
            __DIR__ . '/../../lib/gthreads.so'
        );

        return self::$gthreads;
    }
}
