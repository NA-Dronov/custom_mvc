<?php

class AppHelper {
    public static function checkExpression($expr, $message) 
    {   
        $exception = null;
        try {
            if (!$expr) {
                $exception = new AppException($message);
                throw $exception;
            }
        } catch (Throwable $ex) {
            throw new AppException($message, $ex == $exception ? null: $ex);
        }
    }
}