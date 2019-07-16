<?php

namespace App\UserListeners\Administrator\UpdateAdministrator;

use App\Models\AdminRole;
use App\UserEvents\Administrator\UpdateAdministrator;
use ZhiEq\Contracts\Listener;

class WriteRole extends Listener
{

    /**
     * @param UpdateAdministrator $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $needCreateRoles = array_diff($event->input['roles'], $event->adminModel->roles);
        $needDeleteRoles = array_diff($event->adminModel->roles, $event->input['roles']);
        if (count($needCreateRoles) !== count(array_filter($needCreateRoles, function ($roleCode) use ($event) {
                return (new AdminRole())
                    ->setAttribute('admin_code', $event->adminModel->code)
                    ->setAttribute('role_code', $roleCode)
                    ->save();
            }))) {
            return false;
        }
        if (count($needDeleteRoles) !== count(array_filter($needDeleteRoles, function ($roleCode) use ($event) {
                return AdminRole::whereRoleCode($roleCode)
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
        return 2;
    }
}
