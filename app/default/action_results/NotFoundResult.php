<?php

class NotFoundResult extends ViewResult
{
    public function __construct(IRendererComponent $renderer)
    {
        parent::__construct($renderer, "404");
    }

    public function execute()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        parent::execute();
    }
}
