<?php

namespace App\UserListeners\Role\UpdateRole;

use App\UserEvents\Role\UpdateRole;
use ZhiEq\Contracts\Listener;

class WriteRole extends Listener
{

    /**
     * @param UpdateRole $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->roleModel->setAttribute('name', $event->input['name'])->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
