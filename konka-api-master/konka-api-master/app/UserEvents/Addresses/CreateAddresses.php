<?php

namespace App\UserEvents\Addresses;


use App\Models\UserAddresses;
use Illuminate\Validation\Rule;
use Validator;

class CreateAddresses
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * CreateAddresses constructor.
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
            'name' => ['required'],
            'phone' => ['required'],
            'provinceText' => ['required'],
            'cityText' => ['required'],
            'countyText' => ['required'],
            'address' => ['required'],
            'postalCode' => ['required'],
            'status' => ['required', Rule::in(UserAddresses::getStatusList())]
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'name.required' => '收货人名称不能为空',
            'phone.required' => '收货电话不能为空',
            'provinceText.required' => '省不能为空',
            'cityText.required' => '市不能为空',
            'countyText.required' => '县不能为空',
            'address.required' => '详细地址不能为空',
            'postalCode.required' => '邮政编码不能为空',
            'status.required' => '默认不能为空'
        ];
    }
}