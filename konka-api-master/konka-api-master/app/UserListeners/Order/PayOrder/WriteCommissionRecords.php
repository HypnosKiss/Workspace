<?php

namespace App\UserListeners\Order\PayOrder;

use App\Models\PartnerCommissionRecord;
use App\UserEvents\Order\PayOrder;
use ZhiEq\Contracts\Listener;

class WriteCommissionRecords extends Listener
{

    /**
     * @param PayOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $records = PartnerCommissionRecord::whereOrderCode($event->order->code)->get();
        return $records->count() === $records->filter(function (PartnerCommissionRecord $model) {
                return $model->setAttribute('status', PartnerCommissionRecord::STATUS_WAIT_SETTLEMENT)->save();
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
