<?php

namespace App\Validators;

use ZhiEq\Contracts\Validator;

class MobileValidator extends Validator
{
    public function validator($attribute, $value, $parameters, $validator)
    {
        return preg_match('/^(\+?0?86\-?)?((13\d|14[57]|15[^4,\D]|17\d|18\d|16\d|19\d)\d{8}|170[059]\d{7})$/', $value);
    }
}
