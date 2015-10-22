<?php

namespace Melyssa\Validation;

class Expression
{
    public static function isValid($expression, $value)
    {
        return (preg_match($expression, $value));
    }
}
