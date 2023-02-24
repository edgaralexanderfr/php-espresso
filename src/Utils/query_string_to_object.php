<?php

namespace Espresso\Utils;

use stdClass;

function query_string_to_object(?string $query_string): stdClass
{
    $object = (object)[];

    if (!$query_string) {
        return $object;
    }

    $query_string = str_replace('?', '', $query_string);
    $params = explode('&', $query_string);

    foreach ($params as $param) {
        $pair = explode('=', $param);
        $name = $pair[0] ?? null;
        $value = $pair[1] ?? null;

        if (empty($name)) {
            $name = null;
        }

        if (empty($value)) {
            $value = null;
        }

        if ($name) {
            $object->{$name} = $value;
        }
    }

    return $object;
}
