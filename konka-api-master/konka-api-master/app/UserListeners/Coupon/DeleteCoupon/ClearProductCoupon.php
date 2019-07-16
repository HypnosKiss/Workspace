<?php

namespace App\UserListeners\Coupon\DeleteCoupon;


use App\Models\CouponProduct;
use App\UserEvents\Coupon\DeleteCoupon;
use ZhiEq\Contracts\Listener;

class ClearProductCoupon extends Listener
{

    /**
     * @param DeleteCoupon $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $productCoupon = CouponProduct::whereCouponCode($event->couponModel->code)->get();
        return $productCoupon->count() === $productCoupon->filter(function (CouponProduct $item) {
                return $item->delete();
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}