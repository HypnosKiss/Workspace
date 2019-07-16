<?php

namespace App\UserListeners\Bargain\CreateBargainRecord;


use App\Models\BargainRecord;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteBargainRecord extends Listener
{

    /**
     * @param $event
     * @return array|bool|string
     */
    public function handle($event)
    {
        $event->productModel = new BargainRecord();
        $fields = [
            'open_id', 'user_bargain_product_code', 'price','nickname','avatar'
        ];
        $model = $event->productModel->fillable($fields)->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()));
        if (!$model->save()) {
            throw new CustomException('砍价失败');
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
