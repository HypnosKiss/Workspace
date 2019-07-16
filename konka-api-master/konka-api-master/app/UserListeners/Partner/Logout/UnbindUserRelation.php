<?php

namespace App\UserListeners\Partner\Logout;

use ZhiEq\Contracts\Listener;

class UnbindUserRelation extends Listener
{
    /**
     * @param $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return auth_user()->setAttribute('partner_code', null)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
