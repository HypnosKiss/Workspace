<?php

namespace App\UserEvents\VerificationCode;


class SendSmsCode
{
    /**
     * @var string
     */

    public $code;

    /**
     * @var string
     */

    public $codeId;

    /**
     * @var array
     */

    public $input;

    /**
     * SendSmsCode constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        \Validator::validate($input, [
            'mobile' => ['required', 'zh_mobile']
        ], [
            'mobile.required' => '手机号码不能为空',
            'mobile.zh_mobile' => '手机号码格式不正确'
        ]);
        $this->input = $input;
    }
}
