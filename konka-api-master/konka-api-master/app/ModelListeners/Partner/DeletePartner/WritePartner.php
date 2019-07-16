<?php

namespace App\ModelListeners\Partner\DeletePartner;


use App\ModelEvents\Partner\DeletePartner;
use ZhiEq\Contracts\Listener;

class WritePartner extends Listener
{

    /**
     * @param DeletePartner $event
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
