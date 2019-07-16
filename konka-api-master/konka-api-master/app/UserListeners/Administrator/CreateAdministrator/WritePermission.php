<?php

namespace App\UserListeners\Administrator\CreateAdministrator;

use App\Models\AdminPermission;
use App\UserEvents\Administrator\CreateAdministrator;
use ZhiEq\Contracts\Listener;

class WritePermission extends Listener
{

    /**
     * @param CreateAdministrator $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return isset($event->input['permissions']) ? count($event->input['permissions']) === count(array_filter($event->input['permissions'], function ($permission) use ($event) {
                return (new AdminPermission())
                    ->setAttribute('admin_code', $event->adminModel->code)
                    ->setAttribute('permission', $permission)
                    ->save();
            })) : true;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
