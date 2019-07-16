<?php

namespace App\UserListeners\Bargain\CreateUserBargainProduct;


use App\Models\BargainRecord;
use App\UserEvents\Bargain\CreateUserBargainProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class BargainSelf extends Listener
{

    /**
     * @param CreateUserBargainProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        //砍自己一刀
        if ($event->input) {
            $fields = [
                'open_id', 'user_bargain_product_code', 'price','nickname','avatar'
            ];
            $data = ['open_id'=>auth_user()->userOauth->open_id,'nickname'=>auth_user()->nickname,'avatar'=>auth_user()->avatar,
                'price'=>$event->input['bargainPrice'],'user_bargain_product_code'=>$event->productModel->code];
            $model = new BargainRecord();
            $model = $model->fillable($fields)->fill($data);
            if (!$model->save()) {
                throw new CustomException('砍价失败');
            }
            $ubp = $event->productModel->fill(['bargain_price'=>$event->input['bargainPrice']]);
            if (!$ubp->save()) {
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
        return 1;
    }
}
