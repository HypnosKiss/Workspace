<?php

namespace App\ModelListeners\Admin\DeleteAdmin;

use App\ModelEvents\Admin\DeleteAdmin;
use App\Models\AdminPermission;
use ZhiEq\Contracts\Listener;

class WriteAdminPermission extends Listener
{

    /**
     * @param DeleteAdmin $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return AdminPermission::whereAdminCode($event->model->code)->delete() !== false;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
