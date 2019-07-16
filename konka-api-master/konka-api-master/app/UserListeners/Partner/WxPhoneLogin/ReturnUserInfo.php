<?php

namespace App\UserListeners\Partner\WxPhoneLogin;

use App\UserEvents\Partner\WxPhoneLogin;
use ZhiEq\Contracts\Listener;

class ReturnUserInfo extends Listener
{

    /**
     * @param WxPhoneLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return auth_user()->current();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 4;
    }
}
