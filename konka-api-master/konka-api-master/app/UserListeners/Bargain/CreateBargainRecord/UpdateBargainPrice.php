<?php

namespace App\UserListeners\Bargain\CreateBargainRecord;


use App\Models\UserBargainProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class UpdateBargainPrice extends Listener
{

    /**
     * @param $event
     * @return array|bool|string
     */
    public function handle($event)
    {
        $price = $event->productModel->price;
        $ubp = UserBargainProduct::whereCode($event->input['userBargainProductCode'])->first();
        $ubp->bargain_price = bcadd($ubp->bargain_price, $price, 2);
        if (!$ubp->save()) {
            throw new CustomException('砍价失败');
        }
        return true;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
