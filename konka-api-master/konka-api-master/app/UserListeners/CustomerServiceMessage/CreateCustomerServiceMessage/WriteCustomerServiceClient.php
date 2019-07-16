<?php

namespace App\UserListeners\CustomerServiceMessage\CreateCustomerServiceMessage;

use App\Models\CustomServiceClient;
use App\UserEvents\CustomerServiceMessage\CreateCustomerServiceMessage;
use Carbon\Carbon;
use ZhiEq\Contracts\Listener;

class WriteCustomerServiceClient extends Listener
{

    /**
     * @param CreateCustomerServiceMessage $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$client = CustomServiceClient::whereUserCode(auth_user()->code)->first()) {
            $client = (new CustomServiceClient())->setAttribute('user_code', auth_user()->code);
        }
        return $client->setAttribute('last_message', $event->input['content'])
            ->setAttribute('message_type', $event->input['messageType'])
            ->setAttribute('last_send_at', Carbon::now())
            ->setAttribute('unread_num', $client->unread_num + 1)
            ->setAttribute('status', CustomServiceClient::STATUS_UNREAD)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
