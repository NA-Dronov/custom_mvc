<?php

final class ComponentsProviderEnum
{
    const COMPONENT = 0;
    const CONSTRUCTOR = 1;

    final private function __construct()
    {
        throw new BadMethodCallException(); // 
    }

    final private function __clone()
    {
        throw new BadMethodCallException();
    }

    final public static function toArray(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}

interface IComponentsProvider
{
    function getComponent(string $key, string $className, array $args = []): ?Component;
    function registerComponent(string $key, Component $component);
    function registerConstructor(string $key, string $componentType);
    function getComponentType(string $key): ?string;
}
