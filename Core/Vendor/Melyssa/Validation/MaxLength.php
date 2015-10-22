<?php

namespace Melyssa\Validation;

class MaxLength
{
    public static function isValid($base, $search)
    {
        return (strlen($search) > $base) ? false : true;
    }
}
