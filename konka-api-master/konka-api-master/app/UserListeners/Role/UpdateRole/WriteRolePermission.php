<?php

namespace App\UserListeners\Role\UpdateRole;

use App\Models\RolePermission;
use App\UserEvents\Role\UpdateRole;
use ZhiEq\Contracts\Listener;

class WriteRolePermission extends Listener
{

    /**
     * @param UpdateRole $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $needCreatePermissions = array_diff($event->input['permissions'], $event->roleModel->permissions);
        $needDeletePermissions = array_diff($event->roleModel->permissions, $event->input['permissions']);
        if (count($needCreatePermissions) !== count(array_filter($needCreatePermissions, function ($permission) use ($event) {
                return (new RolePermission())->setAttribute('role_code', $event->roleModel->code)
                    ->setAttribute('permission', $permission)
                    ->save();
            }))) {
            return false;
        }
        if (count($needDeletePermissions) !== count(array_filter($needDeletePermissions, function ($permission) use ($event) {
                return RolePermission::wherePermission($permission)
                    ->whereRoleCode($event->roleModel->code)
                    ->delete();
            }))) {
            return false;
        }
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
