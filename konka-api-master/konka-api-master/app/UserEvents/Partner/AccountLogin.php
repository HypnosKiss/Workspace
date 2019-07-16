<?php

namespace App\UserEvents\Partner;


use App\Models\Partner;

class AccountLogin
{
    /**
     * @var array
     */

    public $input;

    /**
     * @var Partner
     */

    public $model;

    /**
     * AccountLogin constructor.
     * @param array $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct(array $input)
    {
        \Validator::validate($input, $this->rules(), $this->messages());
        $this->input = $input;
    }

    public function rules()
    {
        return [
            'username' => ['required'],
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => '登录名不能为空',
            'password.required' => '登录密码不能为空'
        ];
    }
}
