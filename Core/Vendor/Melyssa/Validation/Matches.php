<?php

namespace Melyssa\Validation;

use Melyssa\Input;

class Matches
{
    public static function isValid($baseValue, $value)
    {
        $ipt = new Input();
        return ($ipt->getPost($baseValue) === $value);
    }
}
