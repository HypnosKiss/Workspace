<?php

namespace App\UserEvents\ProductCategory;


use App\Models\ProductCategory;
use ZhiEq\Exceptions\CustomException;

class DeleteProductCategory
{

    /**
     * @var ProductCategory|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $categoryModel;

    /**
     * DeleteProductCategory constructor.
     * @param $code
     */

    public function __construct($code)
    {
        if (!$this->categoryModel = ProductCategory::whereCode($code)->first()) {
            throw new CustomException('分类编码不存在');
        }
    }
}