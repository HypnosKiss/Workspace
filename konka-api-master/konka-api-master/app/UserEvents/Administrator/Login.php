<?php

namespace App\UserEvents\Administrator;


use App\Models\Admin;
use Validator;

class Login
{
    /**
     * @var array $input
     */
    public $input;

    /**
     * @var Admin $adminModel
     */
    public $adminModel;

    /**
     * Login constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'username' => ['required'],
            'password' => ['required']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'username.required' => '用户名不能为空',
            'password.required' => '密码不能为空'
        ];
    }
}