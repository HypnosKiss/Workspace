<?php

namespace App\Extend\VerificationCode\Validators;

use ZhiEq\Contracts\Validator;

class VerificationCodeValidator extends Validator
{

    /**
     * @param string $attribute
     * @param $value
     * @param array $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */

    public function validator($attribute, $value, $parameters, $validator)
    {
        $data = $validator->getData();
        app('log')->info('validatorCode:' . $data[$parameters[0]]);
        if (isset($data[$parameters[0]])) {
            return app('verification_code')->validateAndDestroySaveCode($data[$parameters[0]], $value);
        }
        return false;
    }
}
