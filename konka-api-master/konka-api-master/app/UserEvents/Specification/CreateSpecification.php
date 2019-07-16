<?php

namespace App\UserEvents\Specification;


use Illuminate\Validation\Rule;
use Validator;

class CreateSpecification
{

    /**
     * @var array $input
     */
    public $input;

    /**
     * CreateSpecification constructor.
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
            'parentCode' => ['nullable', Rule::exists('specifications', 'code')],
            'order' => ['nullable', 'numeric']
        ];
    }

    protected function message()
    {
        return [
            'name.required' => '名称不能为空',
            'parentCode.exists' => '上次分类编码不存在',
            'order.numeric' => '排序必须是数值'
        ];
    }
}