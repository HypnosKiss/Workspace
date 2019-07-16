<?php

namespace App\ModelListeners\Admin\DeleteAdmin;

use App\ModelEvents\Admin\DeleteAdmin;
use App\Models\AdminRole;
use ZhiEq\Contracts\Listener;

class WriteAdminRole extends Listener
{

    /**
     * @param DeleteAdmin $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return AdminRole::whereAdminCode($event->model->code)->delete() !== false;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
