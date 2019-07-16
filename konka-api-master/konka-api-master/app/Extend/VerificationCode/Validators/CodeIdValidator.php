<?php

namespace App\Extend\VerificationCode\Validators;

use ZhiEq\Contracts\Validator;

class CodeIdValidator extends Validator
{
    /**
     * @param string $attribute
     * @param $value
     * @param array $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return mixed
     */
    public function validator($attribute, $value, $parameters, $validator)
    {
        $data = $validator->getData();
        app('log')->info('validatorCodeId:' . $data[$parameters[0]]);
        if (isset($data[$parameters[0]])) {
            return app('verification_code')->validateCodeId($value, $data[$parameters[0]]);
        }
        return false;
    }
}
