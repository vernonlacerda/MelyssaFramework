<?php

namespace Melyssa\Validation;

class PhoneNumber
{

    public function __construct($value)
    {
        if (!preg_match('/^(\()([0-9]{2})(\))([0-9]{8,9})$/', $value)):
            return false;
        else:
            return true;
        endif;
    }

}
