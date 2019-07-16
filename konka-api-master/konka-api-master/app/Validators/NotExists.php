<?php

namespace App\Validators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Contracts\Validator;

class NotExists extends Validator
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
        if (empty($parameters[0])) {
            return false;
        }
        /**
         * @var Model|Builder $model
         */
        $model = new $parameters[0];
        return !$model->where($parameters[1], $value)->exists();
    }
}