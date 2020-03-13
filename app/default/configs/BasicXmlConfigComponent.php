<?php

class BasicXmlConfigComponent extends AConfigComponent
{
    protected function parseConfiguration()
    {
        AppHelper::checkExpression(file_exists($this->configurationPath), "Config file not found");
        $config = @simplexml_load_file($this->configurationPath);
        AppHelper::checkExpression($config instanceof SimpleXMLElement, "Config file was corrupted");
        foreach ($config as $key => $config_option) {
            $this->configuration[(string) $key] = (string) $config_option;
        }
    }

    public static function getConfigurationType(): string
    {
        return "xml";
    }
}
