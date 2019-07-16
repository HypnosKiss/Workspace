<?php

namespace App\UserListeners\Coupon\DeleteCoupon;


use App\UserEvents\Coupon\DeleteCoupon;
use ZhiEq\Contracts\Listener;

class ClearCoupon extends Listener
{

    /**
     * @param DeleteCoupon $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return $event->couponModel->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}