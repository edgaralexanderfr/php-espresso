<?php

namespace Espresso\Http;

abstract class Packet
{
    protected array $headers = [];
    protected string $payload = '';

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        $header = $this->headers[strtolower($name)] ?? null;

        if (!$header) {
            return null;
        }

        $header_pair = explode(': ', $header);

        return $header_pair[1] ?? null;
    }

    public function setHeader(string $name, string $value = null): void
    {
        $header_name = null;
        $header_value = null;

        if ($value) {
            $header_name = $name;
            $header_value = $value;
        } else {
            $header = explode(':', $name);

            $header_name = $header[0] ?? null;
            $header_value = $header[1] ?? null;
        }

        if ($header_name && $header_value) {
            $header_value = trim($header_value);

            $this->headers[strtolower($header_name)] = "$header_name: $header_value";
        }
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): void
    {
        $this->payload = $payload;
    }
}
