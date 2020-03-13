<?php

class HomeController extends AController
{
    protected $mappersClasses = [UsersMapper::class];

    public function __construct($something = [])
    {
    }

    public function index(): IActionResult
    {
        return $this->View();
    }
}
