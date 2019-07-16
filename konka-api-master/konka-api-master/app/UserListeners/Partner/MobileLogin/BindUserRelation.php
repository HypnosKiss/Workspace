<?php

namespace App\UserListeners\Partner\MobileLogin;

use App\UserEvents\Partner\MobileLogin;
use ZhiEq\Contracts\Listener;

class BindUserRelation extends Listener
{

    /**
     * @param MobileLogin $event
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
        return 2;
    }
}
