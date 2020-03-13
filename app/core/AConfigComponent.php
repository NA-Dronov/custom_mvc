<?php

abstract class AConfigComponent extends Component
{
    protected $configuration = [];
    protected $configurationPath = "";

    public function __construct(string $pathToConfiguration, AConfigComponent ...$parents)
    {
        $this->configurationPath = $pathToConfiguration;
        if (count($parents) > 0) {
            foreach ($parents as $parentConfig) {
                $this->configuration = array_merge($this->configuration, $parentConfig->configuration);
            }
        }
        $this->parseConfiguration();
    }

    protected abstract function parseConfiguration();

    public function __get($name)
    {
        if (isset($this->configuration[$name])) {
            return $this->configuration[$name];
        }
    }

    public function __set($name, $value)
    {
        throw new AppException("Trying to modify configuration object");
    }

    public function toArray(): array
    {
        return $this->configuration;
    }

    public abstract static function getConfigurationType(): string;

    public function getInstanceConfigurationType(): string
    {
        return static::getConfigurationType();
    }
}
