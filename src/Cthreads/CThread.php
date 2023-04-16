<?php

namespace Espresso\Cthreads;

abstract class CThread
{
    private static ?\FFI $cthreads = null;

    public abstract function run(): void;

    public function start(): void
    {
        $ffi = self::getFFI();

        $ffi->CT_Exec(function () {
            $this->run();
        });
    }

    private static function getFFI(): \FFI
    {
        if (self::$cthreads) {
            return self::$cthreads;
        }

        self::$cthreads = \FFI::cdef(
            'int CT_Exec(void (*callable)());',
            __DIR__ . '/../../lib/cthreads.so'
        );

        return self::$cthreads;
    }
}
