<?php

namespace App\UserListeners\Addresses\UpdateAddresses;


use App\Models\UserAddresses;
use App\UserEvents\Addresses\UpdateAddresses;
use ZhiEq\Contracts\Listener;

class UpdateAddressesStatus extends Listener
{

    /**
     * @param UpdateAddresses $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if ((int)$event->input['status'] !== UserAddresses::STATUS_DEFAULT) {
            return true;
        }
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