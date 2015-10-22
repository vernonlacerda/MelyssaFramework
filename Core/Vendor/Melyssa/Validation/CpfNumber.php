<?php
namespace Melyssa\Validation;

class CpfNumber{
    public static function isValid($value_unfiltered) {
        $value = preg_replace('/\D/', '', $value_unfiltered);
        if($value === null OR $value === '' OR strlen($value) != 11 OR !preg_match("/^([0-9]{11})$/", $value)){
            return false;
        }else{
            for($s = 10, $n = 0, $i = 0;$s >= 2;$n += $value[$i++] * $s--){}
            if($value[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)){
                return false;
            }
            for($s = 11, $n = 0, $i = 0; $s >= 2; $n += $value[$i++] * $s--){}
            if($value[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)){
                return false;
            }
        }
        
        return true;
    }
}