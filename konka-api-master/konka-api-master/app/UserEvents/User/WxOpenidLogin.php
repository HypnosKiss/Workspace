<?php

namespace App\UserEvents\User;


use App\Models\User;
use App\Models\UserOauth;
use Illuminate\Validation\Rule;
use Validator;

class WxOpenidLogin
{
    /**
     * @var
     */
    public $input;

    /**
     * @var User
     */
    public $user;

    /**
     * @var UserOauth
     */
    public $user_oauth;

    /**
     * wxOpenidLogin constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    protected function rules()
    {
        return [
            'openId' => ['required']
        ];
    }

    protected function message()
    {
        return [
            'type.required' => '类型不能为空',
            'type.in' => '类型不在范围内',
            'openId.required' => 'openId不能为空',
        ];
    }
}
