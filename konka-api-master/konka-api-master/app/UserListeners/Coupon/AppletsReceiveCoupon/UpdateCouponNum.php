<?php

namespace App\UserListeners\Coupon\AppletsReceiveCoupon;


use App\UserEvents\Coupon\AppletsReceiveCoupon;
use ZhiEq\Contracts\Listener;

class UpdateCouponNum extends Listener
{

    /**
     * @param AppletsReceiveCoupon $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->couponModel->setAttribute('num', $event->couponModel->num - 1)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}