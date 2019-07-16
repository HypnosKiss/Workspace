<?php

namespace App\UserListeners\CustomerServiceMessage\ReplyCustomerServiceMessage;


use App\Models\CustomerServiceMessage;
use App\UserEvents\CustomerServiceMessage\ReplyCustomerServiceMessage;
use ZhiEq\Contracts\Listener;

class WriteCustomerServiceMessage extends Listener
{

    /**
     * @param ReplyCustomerServiceMessage $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return (new CustomerServiceMessage())
            ->setAttribute('content', $event->input['content'])
            ->setAttribute('message_type', $event->input['messageType'])
            ->setAttribute('type', CustomerServiceMessage::TYPE_USER)
            ->setAttribute('user_code', $event->customerServiceMessageModel->user_code)
            ->setAttribute('admin_code', auth_admin()->code)
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