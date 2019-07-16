<?php

namespace App\UserListeners\Order\PayOrder;

use App\Models\Order;
use App\UserEvents\Order\PayOrder;
use ZhiEq\Contracts\Listener;

class WriteOrder extends Listener
{

    /**
     * @param PayOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->order->setAttribute('pay_at', $event->payedAt)
            ->setAttribute('pay_number', $event->payNumber)
            ->setAttribute('status', Order::STATUS_NOT_SHIPPED)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
