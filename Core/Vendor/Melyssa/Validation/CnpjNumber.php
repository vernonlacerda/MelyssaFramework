<?php
namespace Melyssa\Validation;

class CnpjNumber
{
    public static function isValid($value_unfiltered)
    {
        $value = preg_replace('/\D/', '', $value_unfiltered);
        $b = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
        if ($value === null or $value === '' or strlen($value) != 14 or !preg_match("/^([0-9]{14})$/", $value)) {
            return false;
        } else {
            for ($i = 0, $n = 0; $i < 12; $n += $value[$i] * $b[++$i]) {
            }
            if ($value[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                return false;
            }
            for ($i = 0, $n = 0; $i <= 12; $n += $value[$i] * $b[$i++]) {
            }
            if ($value[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                return false;
            }
        }

        return true;
    }
}
