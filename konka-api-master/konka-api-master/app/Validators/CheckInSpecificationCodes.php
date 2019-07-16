<?php

namespace App\Validators;


use App\Models\Specification;
use ZhiEq\Contracts\Validator;

class CheckInSpecificationCodes extends Validator
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
        return count($value) === Specification::enable()->whereIn('code', $value)->get()->count();
    }
}