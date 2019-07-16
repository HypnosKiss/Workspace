<?php

namespace App\UserListeners\Coupon\AppletsReceiveCoupon;


use App\Models\Coupon;
use App\Models\CouponUser;
use App\UserEvents\Coupon\AppletsReceiveCoupon;
use ZhiEq\Contracts\Listener;

class WriteUserCoupon extends Listener
{

    /**
     * @param AppletsReceiveCoupon $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->couponModel = Coupon::whereCode($event->input['code'])->first()) {
            return false;
        }
        $fields = [
            'type', 'name', 'discount', 'content', 'start_at', 'end_at',
            'conditions', 'conditions_value'
        ];
        return (new CouponUser())->fillable($fields)->fill($event->couponModel->only($fields))
            ->setAttribute('user_code', auth_user()->code)
            ->setAttribute('coupon_code', $event->couponModel->code)
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