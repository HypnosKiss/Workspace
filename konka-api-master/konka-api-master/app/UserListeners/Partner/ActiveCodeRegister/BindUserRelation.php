<?php

namespace App\UserListeners\Partner\ActiveCodeRegister;

use App\UserEvents\Partner\ActiveCodeRegister;
use ZhiEq\Contracts\Listener;

class BindUserRelation extends Listener
{

    /**
     * @param ActiveCodeRegister $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return auth_user()->setAttribute('partner_code', $event->model->code)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
