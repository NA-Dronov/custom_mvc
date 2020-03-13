<?php

class Component
{
    public final function getType(): string
    {
        return static::class;
    }

    public function __construct(string $class)
    {
    }
}
