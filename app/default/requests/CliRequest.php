<?php

class CliRequest extends ARequest {
    private $rawData = [];

    public function __construct(array $options = [])
    {
        $cmdParams = getopt("u:m:d:p::");
        $method = $cmdParams['m'] ?? '';
        $method = strtoupper($method);

        if (!in_array($method, ["POST", "GET"])) {
            throw new AppException("Unsupported REQUEST_METHOD '{$method}'");
        }

        $data = ['url' => $cmdParams['u'] ?? ''];

        if (isset($cmdParams['d'])) {
            $cmdData = json_decode(is_array($cmdParams['d']) ? array_pop($cmdParams['d']) : $cmdParams['d'], true);
            if (json_last_error() !== 0) {
                throw new AppException("CLI data: " . json_last_error_msg());
            }
            $data = array_merge($data, $cmdData);
        }

        $this->rawData = $data;
        parent::__construct($method, $data, $options);
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    protected function filterData(array $data): array
    {
        return $data;
    }
}