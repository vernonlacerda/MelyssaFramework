<?php

namespace Melyssa\Validation;

use Melyssa\Model;

class BdExists
{
    public static function isValid($base, $value)
    {
        if (strpos($base, '.')) {
            list($table, $field) = explode('.', $base);
            $model = new Model();
            $model->tableName = $table;
            $q = $model->Read("{$field} = '{$value}'");
            if ($model->countResults() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
