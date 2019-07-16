<?php

namespace App\UserEvents\Product;


use App\Models\Product;
use App\Models\Specification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class UpdateProduct
{
    /**
     * @var array $input
     */
    public $input;

    /**
     * @var Product $productModel
     */
    public $productModel;

    /**
     * @var Specification[]|\Illuminate\Database\Eloquent\Collection
     */
    public $specifications;

    /**
     * @var
     */

    public $specificationCategory = [];

    /**
     * @var Collection
     */

    public $specificationArray;

    /**
     * @var array
     */

    public $deleteSpecification;

    /**
     * CreateProduct constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    /**
     * UpdateProduct constructor.
     * @param $input
     * @param $code
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $code)
    {
        if (!$this->productModel = Product::whereCode($code)->first()) {
            throw new CustomException('产品编码不存在');
        }
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    protected function rules()
    {
        return [
            'title' => ['required'],
            'mainImage' => ['required'],
            'images' => ['required', 'array', 'max:6', 'min:1'],
            'content' => ['required', 'array', 'min:1', 'max:5'],
            'productCategoryCode' => ['required', Rule::exists('product_categories', 'code')],
            'specification' => ['required', 'array', 'min:1']
        ];
    }

    protected function message()
    {
        return [
            'title.required' => '标题不能为空',
            'mainImage.required' => '缩略图不能为空',
            'images.required' => '主图不能为空',
            'images.array' => '主图必须是数组',
            'images.max' => '主图最多6张',
            'images.min' => '至少上传一张宣传图',
            'content.required' => '至少上传一张内容图',
            'content.array' => '内容图必须是数组',
            'content.min' => '至少上传一张内容图',
            'content.max' => '内容图最多5张',
            'productCategoryCode.required' => '分类不能为空',
            'productCategoryCode.exists' => '分类不存在',
            'specification.required' => '产品规格不能为空',
            'specification.array' => '产品规格必须是数组',
            'specification.min' => '至少选择一个产品规格',
        ];
    }
}
