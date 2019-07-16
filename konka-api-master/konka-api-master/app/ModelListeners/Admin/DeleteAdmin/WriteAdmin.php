<?php

namespace App\ModelListeners\Admin\DeleteAdmin;

use App\ModelEvents\Admin\DeleteAdmin;
use ZhiEq\Contracts\Listener;

class WriteAdmin extends Listener
{

    /**
     * @param DeleteAdmin $event
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
        return 2;
    }
}
