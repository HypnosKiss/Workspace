<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;

use App\Models\BaseInfo;
use App\Models\Order;
use App\UserEvents\Order\CreateOrder;
use ZhiEq\Contracts\Listener;

class WeChatPay extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        if ((int)$event->input['payType'] !== Order::PAY_TYPE_WE_CHAT) {
            return ['message' => BaseInfo::getSetting(BaseInfo::TYPE_EXCESS_REMINDER)];
        }
        return $event->orderModel->WeChatPay();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 9;
    }
}
