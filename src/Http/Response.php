<?php

namespace Espresso\Http;

class Response extends Packet
{
    private int $status_code = 200;

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function setStatusCode(int $status_code): void
    {
        $this->status_code = $status_code;
    }

    public function getStatus(): string
    {
        $status_code = $this->getStatusCode();

        if (!isset(CODES[$status_code])) {
            $status_code = 200;
        }

        $status_text = CODES[$status_code];

        return "$status_code $status_text";
    }

    public function send($body, int $status_code = 200, array $headers = []): bool
    {
        if (!$headers) {
            $headers = [
                'Content-Type' => 'application/json',
            ];
        }

        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        $response = json_encode($body);

        $this->setStatusCode($status_code);

        return $this->setPayload($response);
    }
}
