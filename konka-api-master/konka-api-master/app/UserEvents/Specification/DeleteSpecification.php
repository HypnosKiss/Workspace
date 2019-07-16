<?php

namespace App\UserEvents\Specification;


use App\Models\ProductSpecification;
use App\Models\Specification;
use ZhiEq\Exceptions\CustomException;

class DeleteSpecification
{
    /**
     * @var Specification|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $specificationModel;

    /**
     * DeleteSpecification constructor.
     * @param $code
     */

    public function __construct($code)
    {
        if (!$this->specificationModel = Specification::whereCode($code)->first()) {
            throw new CustomException('规格编码不存在');
        }
        if (ProductSpecification::where('specification_codes', 'like', '%' . $code . '%')->exists()) {
            throw new CustomException('当前规格已绑定产品,不能删除');
        }
    }
}