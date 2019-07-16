<?php

namespace App\UserListeners\Administrator\UpdateAdministrator;

use App\Models\AdminPermission;
use App\UserEvents\Administrator\UpdateAdministrator;
use ZhiEq\Contracts\Listener;

class WritePermission extends Listener
{

    /**
     * @param UpdateAdministrator $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $needCreatePermissions = array_diff($event->input['permissions'], $event->adminModel->permissions);
        $needDeletePermissions = array_diff($event->adminModel->permissions, $event->input['permissions']);
        if (count($needCreatePermissions) !== count(array_filter($needCreatePermissions, function ($permission) use ($event) {
                return (new AdminPermission())
                    ->setAttribute('admin_code', $event->adminModel->code)
                    ->setAttribute('permission', $permission)
                    ->save();
            }))) {
            return false;
        }
        if (count($needDeletePermissions) !== count(array_filter($needDeletePermissions, function ($permission) use ($event) {
                return AdminPermission::wherePermission($permission)
                    ->whereAdminCode($event->adminModel->code)
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
        return 1;
    }
}
