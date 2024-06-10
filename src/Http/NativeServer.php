<?php

namespace Espresso\Http;

class NativeServer
{
    private const FFI_HEADER_FILE_PATH = __DIR__ . '/../../include/espresso.h';
    private const FFI_LIB_PATH = __DIR__ . '/../../lib/espresso.so';

    /** @var object */
    private \FFI $ffi;

    public function __construct()
    {
        $this->ffi = \FFI::cdef(
            file_get_contents(self::FFI_HEADER_FILE_PATH),
            self::FFI_LIB_PATH
        );
    }

    public function getFFI(): \FFI
    {
        return $this->ffi;
    }

    public function setErrorCallable(callable $callable): void
    {
        $this->ffi->setEspressoErrorCallable($callable);
    }

    public function setHttpServerCallable(callable $callable): void
    {
        $this->ffi->setEspressoHttpServerCallable($callable);
    }

    public function listenServer(int $port): void
    {
        $this->ffi->listenEspressoServer($port);
    }
}
