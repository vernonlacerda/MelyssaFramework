<?php

namespace Melyssa\Validation;

class ExactLength
{

    public static function isValid($base, $search)
    {
        return (strlen($search) == $base) ? true : false;
    }

}
