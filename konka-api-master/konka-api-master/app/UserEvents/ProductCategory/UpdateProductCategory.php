<?php

namespace App\UserEvents\ProductCategory;


use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class UpdateProductCategory
{
    /**
     * @var array $input
     */
    public $input;

    /**
     * @var ProductCategory|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $categoryModel;

    /**
     * UpdateProductCategory constructor.
     * @param $input
     * @param $code
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $code)
    {
        if (!$this->categoryModel = ProductCategory::whereCode($code)->first()) {
            throw new CustomException('分类编码不存在');
        }
        if (ProductCategory::whereParentCode($this->categoryModel->code)->exists() && !empty($this->categoryModel->parent_code)) {
            throw new CustomException('存在下级分类不能修改上级分类为其他分类');
        }
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
