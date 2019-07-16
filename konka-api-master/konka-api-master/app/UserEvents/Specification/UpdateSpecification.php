<?php

namespace App\UserEvents\Specification;


use App\Models\Specification;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class UpdateSpecification
{
    /**
     * @var array $input
     */
    public $input;

    /**
     * @var Specification|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $specificationModel;

    /**
     * UpdateProductCategory constructor.
     * @param $input
     * @param $code
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $code)
    {
        if (!$this->specificationModel = Specification::whereCode($code)->first()) {
            throw new CustomException('规格编码不存在');
        }
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