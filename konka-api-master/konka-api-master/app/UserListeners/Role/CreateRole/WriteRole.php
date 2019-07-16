<?php

namespace App\UserListeners\Role\CreateRole;

use App\Models\Role;
use App\UserEvents\Role\CreateRole;
use ZhiEq\Contracts\Listener;

class WriteRole extends Listener
{

    /**
     * @param CreateRole $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $event->roleModel = new Role();
        return $event->roleModel->setAttribute('name', $event->input['name'])->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
