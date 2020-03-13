<?php

interface IDataComponent
{
    function getDSN(): string;
    function getConnection(): PDO;
    static function createFromConfiguration(AConfigComponent $configuration): self;
}
