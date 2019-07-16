<?php

namespace App\UserEvents\Partner;


use App\Models\Partner;

class ResetPassword
{
    /**
     * @var Partner
     */

    public $partner;

    /**
     * @var array
     */

    public $input;

    /**
     * ResetPassword constructor.
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
            'mobile' => ['required', 'zh_mobile'],
            'codeId' => ['required', 'verification_code_id:mobile'],
            'code' => ['required', 'verification_code:codeId'],
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'mobile.required' => '手机号码不能为空',
            'mobile.zh_mobile' => '手机号码格式不正确',
            'codeId.required' => '短信ID不能为空',
            'codeId.verification_code_id' => '短信ID不正确',
            'code.required' => '短信验证码不能为空',
            'code.verification_code' => '短信验证码不正确',
            'password.required' => '登录密码不能为空'
        ];
    }
}
