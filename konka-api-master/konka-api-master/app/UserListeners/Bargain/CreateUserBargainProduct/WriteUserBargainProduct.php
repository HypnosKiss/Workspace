<?php

namespace App\UserListeners\Bargain\CreateUserBargainProduct;


use App\Models\UserBargainProduct;
use App\UserEvents\Bargain\CreateUserBargainProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteUserBargainProduct extends Listener
{

    /**
     * @param CreateUserBargainProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if ($event->input) {
            $event->productModel = new UserBargainProduct();
            $fields = [
                'user_code', 'bargain_product_code', 'bargain_price'
            ];
            $model = $event->productModel->fillable($fields)->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()));
            if (!$model->save()) {
                throw new CustomException('砍价失败');
            }
        }
        return true;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
