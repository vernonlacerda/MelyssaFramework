<?php
namespace Melyssa\Validation;

class Url
{
    public function __construct($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)):
            return false; else:
            return true;
        endif;
    }
}
