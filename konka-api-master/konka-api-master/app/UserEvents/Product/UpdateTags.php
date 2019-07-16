<?php

namespace App\UserEvents\Product;

use App\Models\Product;
use App\Models\ProductTag;
use ZhiEq\Exceptions\CustomException;

class UpdateTags
{
    /**
     * @var \Illuminate\Support\Collection
     */

    public $needCreateTags;

    /**
     * @var \Illuminate\Support\Collection
     */

    public $needDeleteTags;

    /**
     * @var Product|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */

    public $productModel;

    /**
     * UpdateTags constructor.
     * @param $code
     * @param $input
     */

    public function __construct($code, $input)
    {
        if (!$this->productModel = Product::whereCode($code)->first()) {
            throw new CustomException('产品不存在');
        }
        $needTags = collect($input);
        $hasTags = ProductTag::whereProductCode($code)->get()->pluck('tag_code');
        $this->needCreateTags = $needTags->diff($hasTags);
        $this->needDeleteTags = $hasTags->diff($needTags);
    }
}
