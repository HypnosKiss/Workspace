<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;


use App\Models\CouponUser;
use App\UserEvents\Order\CreateOrder;
use ZhiEq\Contracts\Listener;

class UpdateUserCouponStatus extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (empty($event->input['userCouponCode'])) {
            return true;
        }
        $userCoupon = CouponUser::whereCode($event->input['userCouponCode'])->first();
        return $userCoupon->setAttribute('status', CouponUser::STATUS_HAS)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 3;
    }
}
