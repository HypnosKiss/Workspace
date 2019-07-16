<?php

namespace App\UserListeners\Addresses\UpdateStatus;


use App\Models\UserAddresses;
use App\UserEvents\Addresses\UpdateStatus;
use ZhiEq\Contracts\Listener;

class UpdateDefaultStatus extends Listener
{

    /**
     * @param UpdateStatus $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->addressesModel->setAttribute('status', UserAddresses::STATUS_DEFAULT)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}