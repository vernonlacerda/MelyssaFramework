<?php
namespace Melyssa\Validation;

class AlphaNumeric{
    public static function isValid($value) {
        return preg_match('/^([a-zA-Zà-úÀ-Ú0-9 ]{1,})$/', $value);
    }
}