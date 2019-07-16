<?php

namespace App\UserListeners\Administrator\CreateAdministrator;

use App\Models\AdminRole;
use App\UserEvents\Administrator\CreateAdministrator;
use ZhiEq\Contracts\Listener;

class WriteRole extends Listener
{

    /**
     * @param CreateAdministrator $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return isset($event->input['roles']) ? count($event->input['roles']) === count(array_filter($event->input['roles'], function ($roleCode) use ($event) {
                return (new AdminRole())
                    ->setAttribute('admin_code', $event->adminModel->code)
                    ->setAttribute('role_code', $roleCode)
                    ->save();
            })) : true;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 2;
    }
}
