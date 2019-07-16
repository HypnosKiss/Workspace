<?php

namespace App\UserListeners\Coupon\CreateCoupon;


use App\Models\Coupon;
use App\UserEvents\Coupon\CreateCoupon;
use ZhiEq\Contracts\Listener;

class WriteCoupon extends Listener
{

    /**
     * @param CreateCoupon $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'type', 'name', 'discount', 'num', 'content', 'start_at',
            'end_at', 'conditions', 'conditions_value', 'limit', 'status'
        ];
        return (new Coupon())->fillable($fields)->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()))
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