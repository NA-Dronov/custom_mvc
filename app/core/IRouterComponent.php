<?php

interface IRouterComponent {
    function parseURL(string $url);
    function getController(): string;
    function getAction(): string;
    function getParams(): array;
} 