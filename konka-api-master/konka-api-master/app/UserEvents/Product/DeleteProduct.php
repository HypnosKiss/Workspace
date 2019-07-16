<?php

namespace App\UserEvents\Product;


use App\Models\Product;
use ZhiEq\Exceptions\CustomException;

class DeleteProduct
{
    /**
     * @var Product|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $productModel;

    /**
     * DeleteProduct constructor.
     * @param $code
     */

    public function __construct($code)
    {
        if (!$this->productModel = Product::whereCode($code)->first()) {
            throw new CustomException('产品编码不存在');
        }
    }
}