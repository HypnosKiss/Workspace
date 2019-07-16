<?php

namespace App\UserListeners\Coupon\UpdateCoupon;

use App\UserEvents\Coupon\UpdateCoupon;
use ZhiEq\Contracts\Listener;

class WriteCoupon extends Listener
{

    /**
     * @param UpdateCoupon $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'type', 'name', 'discount', 'num', 'content', 'start_at',
            'end_at', 'conditions', 'conditions_value', 'limit', 'status'
        ];
        return $event->couponModel->fillable($fields)->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()))
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