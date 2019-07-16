<?php

namespace App\ModelListeners\Roles\DeleteRole;

use App\ModelEvents\Roles\DeleteRole;
use App\Models\RolePermission;
use ZhiEq\Contracts\Listener;

class WriteRolePermission extends Listener
{

    /**
     * @param DeleteRole $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return RolePermission::whereRoleCode($event->model->code)->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
