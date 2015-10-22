<?php

namespace Melyssa\Validation;

class EmailAddress
{

    public static function isValid($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)):
            return false;
        else:
            return true;
        endif;
    }

}
