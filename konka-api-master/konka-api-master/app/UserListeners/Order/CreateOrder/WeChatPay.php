<?php

namespace App\UserListeners\Order\CreateOrder;

use App\Models\Order;
use App\Models\Setting;
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
        return $event->orderModel->pay_type === Order::PAY_TYPE_WE_CHAT ? array_merge([
            'payType' => Order::PAY_TYPE_WE_CHAT,
            'orderCode' => $event->orderModel->code
        ], $event->orderModel->WeChatPay()) : [
            'payType' => Order::PAY_TYPE_TRANSFER,
            'orderCode' => $event->orderModel->code,
            'amountLimit' => Setting::getValue(Setting::SETTING_KEY_ORDER_WX_PAY_LIMIT_AMOUNT),
            'bankName' => Setting::getValue(Setting::SETTING_KEY_ORDER_PAY_BANK_NAME),
            'bankAccount' => Setting::getValue(Setting::SETTING_KEY_ORDER_PAY_BANK_ACCOUNT),
            'bankOpen' => Setting::getValue(Setting::SETTING_KEY_ORDER_PAY_BANK_OPEN),
        ];
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 9;
    }
}
