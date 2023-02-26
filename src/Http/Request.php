<?php

namespace Espresso\Http;

use stdClass;
use function Espresso\Utils\query_string_to_object;

class Request extends Packet
{
    private ?string $id;
    private stdClass $query_string;

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setQueryString(?string $query_string): void
    {
        $this->query_string = query_string_to_object($query_string);
    }

    public function getQuery(): stdClass
    {
        return $this->getQueryString();
    }

    public function getQueryString(): stdClass
    {
        return $this->query_string;
    }

    public function getParam(string $name): string|bool|null
    {
        return $this->query_string->{$name} ?? null;
    }
}
