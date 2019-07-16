<?php

namespace App\UserListeners\Partner\MobileLogin;

use App\UserEvents\Partner\MobileLogin;
use ZhiEq\Contracts\Listener;

class ReturnUserInfo extends Listener
{

    /**
     * @param MobileLogin $event
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
        return 3;
    }
}
