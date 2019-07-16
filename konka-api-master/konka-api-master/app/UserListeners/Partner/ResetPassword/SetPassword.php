<?php

namespace App\UserListeners\Partner\ResetPassword;


use App\UserEvents\Partner\ResetPassword;
use ZhiEq\Contracts\Listener;

class SetPassword extends Listener
{

    /**
     * @param ResetPassword $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->partner->setAttribute('password',$event->input['password'])->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
