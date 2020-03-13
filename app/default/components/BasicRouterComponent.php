<?php

class BasicRouterComponent extends Component implements IRouterComponent
{
    private $currentController = '';
    private $currentAction = '';
    private $currentParams = [];

    public function __construct(AConfigComponent $config)
    {
        $this->currentController = $config->default_controller ?? '';
        $this->currentAction = $config->default_action ?? '';
    }

    public function parseURL(string $url = '')
    {
        if (!empty($url)) {
            $tempUrl = rtrim($url, '/');
            $tempUrl = filter_var($url, FILTER_SANITIZE_URL);
            $tempUrl = explode('/', $url);

            if (isset($tempUrl[0])) {
                $this->currentController = strtolower(array_shift($tempUrl));
            }

            if (isset($tempUrl[0])) {
                $this->currentAction = strtolower(array_shift($tempUrl));
            }

            if (count($tempUrl) > 0) {
                $this->currentParams = $tempUrl;
            }
        }
    }

    public function getController(): string
    {
        return $this->currentController;
    }

    public function getAction(): string
    {
        return $this->currentAction;
    }

    public function getParams(): array
    {
        return $this->currentParams;
    }
}
