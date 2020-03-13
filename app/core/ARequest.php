<?php

abstract class ARequest
{
    protected $data = [];
    private $method = '';
    protected $url = '';

    public function __construct(string $method, array $data, array $options = [])
    {
        $this->method = $method;
        if (isset($data['url'])) {
            $this->url = $data['url'];
            unset($data['url']);
        }
        $this->data = $this->filterData($data);
    }

    public abstract function getRawData(): array;

    protected abstract function filterData(array $data): array;

    public function __get($name)
    {
        if (in_array($name, ['url', 'method', 'data', true])) {
            return $this->$name;
        } else if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    public function __set($name, $value)
    {
        throw new AppException("Trying to modify request object");
    }
}
