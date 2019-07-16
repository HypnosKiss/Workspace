<?php

namespace App\UserListeners\Order\UpdateTrackingNumber;


use App\UserEvents\Order\UpdateTrackingNumber;
use ZhiEq\Contracts\Listener;

class UpdateOrderTrackingNumber extends Listener
{

    /**
     * @param UpdateTrackingNumber $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->orderModel->setAttribute('tracking_number', $event->trackingNumber)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}