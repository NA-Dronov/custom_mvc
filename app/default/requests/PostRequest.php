<?php

class PostRequest extends ARequest {
    public function __construct(array $options = [])
    {
        parent::__construct('POST', $_POST, $options);
    }

    public function getRawData(): array
    {
        return $_POST;
    }

    protected function filterData(array $data): array
    {
        return $data;
    }
}