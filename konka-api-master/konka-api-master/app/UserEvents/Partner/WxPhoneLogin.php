<?php

namespace App\UserEvents\Partner;


use App\Models\Partner;

class WxPhoneLogin
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
     * @var string
     */

    public $mobile;

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

    /**
     * @return array
     */

    public function rules()
    {
        return [
            'encryptData' => ['required'],
            'iv' => ['required'],
        ];
    }

    /**
     * @return array
     */

    public function messages()
    {
        return [
            'encryptData.required' => 'encryptData不能为空',
            'iv.required' => 'iv不能为空',
        ];
    }
}
