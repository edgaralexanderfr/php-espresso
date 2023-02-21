<?php

namespace Espresso\Event;

class Loop
{
    private static array $events = [];

    public static function schedule(callable $callable): void
    {
        self::$events[] = $callable;
    }

    public static function process(): void
    {
        while (self::$events) {
            foreach (self::$events as $i => $event) {
                if ($event() !== false) {
                    unset(self::$events[$i]);
                }
            }
        }
    }
}
