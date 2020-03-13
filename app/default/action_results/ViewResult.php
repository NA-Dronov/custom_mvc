<?php

class ViewResult implements IActionResult
{
    private $renderer = null;
    private $view = "";
    private $args = [];

    public function __construct(IRendererComponent $renderer, string $view = "index", array $args = [])
    {
        $this->renderer = $renderer;
        $this->view = $view;
        $this->args = $args;
    }

    public function execute()
    {
        $this->renderer->render($this->view, $this->args);
    }
}
