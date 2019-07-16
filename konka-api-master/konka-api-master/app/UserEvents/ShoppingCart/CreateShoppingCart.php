<?php

namespace App\UserEvents\ShoppingCart;


use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Validator;

class CreateShoppingCart
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * CreateShoppingCart constructor.
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
            'productSpecificationCode' => ['required'],
            'num' => ['required', 'numeric']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'productSpecificationCode.required' => '产品编码不能为空',
            'num.required' => '数量不能为空',
            'num.numeric' => '数量必须是数量'
        ];
    }
}
