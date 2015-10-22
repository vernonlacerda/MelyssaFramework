<?php
namespace Melyssa\Validation;

class Alpha{
    public static function isValid($value) {
        return preg_match('/^([a-zA-Zà-úÀ-Ú ]{1,})$/', $value);
    }
}