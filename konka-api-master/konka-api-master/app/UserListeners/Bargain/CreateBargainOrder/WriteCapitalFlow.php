<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;


use App\Models\CapitalFlow;
use App\UserEvents\Order\CreateOrder;
use ZhiEq\Contracts\Listener;

class WriteCapitalFlow extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return (new CapitalFlow())->setAttribute('user_code', auth_user()->code)
            ->setAttribute('money_change', $event->input['actuallyPayPrice'])
            ->setAttribute('final_money', 0)
            ->setAttribute('type', CapitalFlow::TYPE_EXPENDITURE)
            ->setAttribute('order_code', $event->orderModel->code)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 7;
    }
}
