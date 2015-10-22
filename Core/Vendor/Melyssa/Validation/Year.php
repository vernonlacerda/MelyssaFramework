<?php

namespace Melyssa\Validation;

class Year
{
    public static function isValid($base, $search)
    {
        if (!preg_match('/^([0-9]{4})$/', $search)) {
            // Somente 4 dígitos numéricos serão aceitos como ano:
            return false;
        } elseif ('0000' != $base and $search < $base or $search > date('Y') + 1) {
            // Se o usuário setar um ano como sendo o mais antigo possivel
            // e o valor digitado for menor que este ano, ou
            // o valor digitado for maior que o ano corrente retornameos falso:
            return false;
        } else {
            return true;
        }
    }
}
