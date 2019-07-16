<?php

namespace App\ModelListeners\Roles\DeleteRole;

use App\ModelEvents\Roles\DeleteRole;
use ZhiEq\Contracts\Listener;

class WriteRole extends Listener
{

    /**
     * @param DeleteRole $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return $event->model->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
