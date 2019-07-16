<?php

namespace App\ModelListeners\UserInvoice\DeleteInvoice;

use App\ModelEvents\UserInvoice\DeleteInvoice;
use ZhiEq\Contracts\Listener;

class WriteUserInvoice extends Listener
{

    /**
     * @param DeleteInvoice $event
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
