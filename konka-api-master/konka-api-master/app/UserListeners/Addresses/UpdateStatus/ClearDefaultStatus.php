<?php

namespace App\UserListeners\Addresses\UpdateStatus;


use App\Models\UserAddresses;
use App\UserEvents\Addresses\UpdateStatus;
use ZhiEq\Contracts\Listener;

class ClearDefaultStatus extends Listener
{

    /**
     * @param UpdateStatus $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$addresses = UserAddresses::whereUserCode(auth_user()->code)->where('code', '<>', $event->addressesModel->code)
            ->whereStatus(UserAddresses::STATUS_DEFAULT)->first()) {
            return true;
        }
        return $addresses->setAttribute('status', UserAddresses::STATUS_NO_DEFAULT)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}