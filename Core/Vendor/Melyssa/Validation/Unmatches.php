<?php

namespace Melyssa\Validation;

#use Melyssa\Input;

class Unmatches
{
    public static function isValid($baseValue, $value)
    {
        return ($baseValue !== $value);
    }
}
