<?php

class AppException extends Exception {

    private $internalError = null;

    public function __construct(string $message, Throwable $internalError = null)
    {
        $this->message = isset($internalError) ? "{$message}\nInternal error: {$internalError->getMessage()}" : $message;
        $this->internalError = $internalError;
    }
}