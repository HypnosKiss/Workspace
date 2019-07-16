<?php

namespace App\UserListeners\Role\CreateRole;

use App\Models\RolePermission;
use App\UserEvents\Role\CreateRole;
use ZhiEq\Contracts\Listener;

class WriteRolePermission extends Listener
{

    /**
     * @param CreateRole $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return count($event->input['permissions']) === count(array_filter($event->input['permissions'], function ($permission) use ($event) {
                return (new RolePermission())->setAttribute('role_code', $event->roleModel->code)
                    ->setAttribute('permission', $permission)
                    ->save();
            }));
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
