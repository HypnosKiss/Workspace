<?php

namespace App\UserEvents\ProductCategory;


use Illuminate\Validation\Rule;
use Validator;

class CreateProductCategory
{
    /**
     * @var array $input
     */
    public $input;

    /**
     * CreateProductCategory constructor.
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
            'name' => ['required'],
            'parentCode' => ['nullable', Rule::exists('product_categories', 'code')]
        ];
    }

    protected function message()
    {
        return [
            'name.required' => '名称不能为空',
            'parentCode.exists' => '上次分类编码不存在'
        ];
    }
}