<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;

use App\Models\OrderBargain;
use ZhiEq\Contracts\Listener;

class WriteBargain extends Listener
{

    /**
     * @param $event
     * @return array|bool|string
     */
    public function handle($event)
    {

        $fields = [
            'order_code', 'user_bargain_product_code', 'bargain_people', 'price', 'begin_price', 'float_price',
            'product_code', 'product_specifications_code','start_at','end_at'
        ];
        $event->bargainOrder = new OrderBargain();
        return $event->bargainOrder->fillable($fields)
            ->setAttribute('order_code', $event->orderModel->code)
            ->setAttribute('user_bargain_product_code', $event->userBargainProduct->code)
            ->setAttribute('bargain_people',$event->userBargainProduct->record->count())
            ->setAttribute('price', $event->bargainProduct->after_price)
            ->setAttribute('begin_price',$event->bargainProduct->begin_price)
            ->setAttribute('float_price', $event->bargainProduct->float_price)
            ->setAttribute('product_code', $event->orderProduct[0]->product_code)
            ->setAttribute('product_specifications_code', $event->orderProduct[0]->product_specifications_code)
            ->setAttribute('start_at', $event->bargainProduct->start_at)
            ->setAttribute('end_at', $event->bargainProduct->end_at)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 8;
    }
}
