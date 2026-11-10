<?php

namespace App\Utils;

class ErrorBuilder {
    public static function getError(\Throwable $e) {
        return $e->getMessage() . ' *** ' . 
            $e->getFile() . ' *** ' . 
            $e->getLine();
    }
}