<?php

class GetRequest extends ARequest {
    public function __construct(array $options = [])
    {
        parent::__construct('GET', $_GET, $options);
    }

    public function getRawData(): array
    {
        return $_GET;
    }

    protected function filterData(array $data): array
    {
        return $data;
    }
}