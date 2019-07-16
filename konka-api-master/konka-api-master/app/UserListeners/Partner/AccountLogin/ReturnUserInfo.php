<?php

namespace App\UserListeners\Partner\AccountLogin;

use ZhiEq\Contracts\Listener;

class ReturnUserInfo extends Listener
{

    /**
     * @param $event
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
