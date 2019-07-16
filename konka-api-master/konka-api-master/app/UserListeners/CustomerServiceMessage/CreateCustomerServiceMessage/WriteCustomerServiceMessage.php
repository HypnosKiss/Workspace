<?php

namespace App\UserListeners\CustomerServiceMessage\CreateCustomerServiceMessage;


use App\Models\CustomerServiceMessage;
use App\UserEvents\CustomerServiceMessage\CreateCustomerServiceMessage;
use ZhiEq\Contracts\Listener;

class WriteCustomerServiceMessage extends Listener
{

    /**
     * @param CreateCustomerServiceMessage $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return (new CustomerServiceMessage())->setAttribute('content', $event->input['content'])
            ->setAttribute('message_type', $event->input['messageType'])
            ->setAttribute('type', CustomerServiceMessage::TYPE_USER)
            ->setAttribute('user_code', auth_user()->code)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}