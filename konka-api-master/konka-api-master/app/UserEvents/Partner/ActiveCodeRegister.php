<?php

namespace App\UserEvents\Partner;

use App\Models\Partner;
use Illuminate\Validation\Rule;

class ActiveCodeRegister
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
     * ActiveCodeRegister constructor.
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
            'activeCode' => ['required'],
            'mobile' => ['required', 'zh_mobile', Rule::unique('partners', 'client_phone')],
            'codeId' => ['required', 'verification_code_id:mobile'],
            'code' => ['required', 'verification_code:codeId'],
            'password' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'activeCode.required' => '激活码不能为空',
            'mobile.required' => '手机号码不能为空',
            'mobile.zh_mobile' => '手机号码格式不正确',
            'mobile.unique' => '手机号码已绑定其他合伙人',
            'codeId.required' => '短信ID不能为空',
            'codeId.verification_code_id' => '短信ID不正确',
            'code.required' => '短信验证码不能为空',
            'code.verification_code' => '短信验证码不正确',
            'province.required' => '省份不能为空',
            'city.required' => '城市不能为空',
            'county.required' => '县城不能为空',
            'password.required' => '密码不能为空'
        ];
    }
}
