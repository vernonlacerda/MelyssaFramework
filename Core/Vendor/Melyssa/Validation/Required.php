<?php

namespace Melyssa\Validation;

class Required
{
    public static function isValid($value)
    {
        return ($value === '') ? false : true;
    }
}
