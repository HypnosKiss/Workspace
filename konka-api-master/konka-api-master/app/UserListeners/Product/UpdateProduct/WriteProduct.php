<?php

namespace App\UserListeners\Product\UpdateProduct;


use App\UserEvents\Product\UpdateProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteProduct extends Listener
{

    /**
     * @param UpdateProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'product_category_code', 'title', 'sub_title', 'order', 'is_hot', 'is_recommend', 'is_new', 'specification_array', 'konka_product_code'
        ];
        return $event->productModel->fillable($fields)
            ->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()))
            ->setAttribute('specification_array', $event->specificationArray)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
