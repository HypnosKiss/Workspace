<?php

namespace App\UserListeners\Partner\ActiveCodeRegister;

use App\Models\Partner;
use App\UserEvents\Partner\ActiveCodeRegister;
use ZhiEq\Contracts\Listener;

class WritePartner extends Listener
{

    /**
     * @param ActiveCodeRegister $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->model
            ->setAttribute('client_phone', $event->input['mobile'])
//            ->setAttribute('province', $event->input['province'])
//            ->setAttribute('city', $event->input['city'])
//            ->setAttribute('county', $event->input['county'])
            ->setAttribute('password', $event->input['password'])
            ->setAttribute('activation_code', null)
            ->setAttribute('status', Partner::STATUS_ENABLED)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 2;
    }
}
