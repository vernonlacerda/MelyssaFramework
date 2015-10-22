<?php

namespace Melyssa\Validation;

class Number
{
    public static function isValid($value)
    {
        return (is_numeric($value));
    }
}
