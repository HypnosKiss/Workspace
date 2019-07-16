<?php

namespace App\UserListeners\Partner\WxPhoneLogin;

use App\UserEvents\Partner\WxPhoneLogin;
use ZhiEq\Contracts\Listener;

class BindUserRelation extends Listener
{

    /**
     * @param WxPhoneLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (empty(auth_user()->partner_code)) {
            return auth_user()->setAttribute('partner_code', $event->model->code)->save();
        }
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 3;
    }
}
