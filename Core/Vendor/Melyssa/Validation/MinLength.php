<?php

namespace Melyssa\Validation;

class MinLength
{

    public static function isValid($base, $search)
    {
        return (strlen($search) < $base) ? false : true;
    }

}
