<?php

namespace App\UserListeners\Addresses\CreateAddresses;


use App\Models\UserAddresses;
use App\UserEvents\Addresses\CreateAddresses;
use ZhiEq\Contracts\Listener;

class UpdateAddressesStatus extends Listener
{

    /**
     * @param CreateAddresses $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if ((int)$event->input['status'] !== UserAddresses::STATUS_DEFAULT) {
            return true;
        }
        if (!$addresses = UserAddresses::whereUserCode(auth_user()->code)->whereStatus(UserAddresses::STATUS_DEFAULT)->first()) {
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