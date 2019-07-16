<?php

namespace App\ModelListeners\CommissionRules\DeleteRule;

use App\ModelEvents\CommissionRules\DeleteRule;
use ZhiEq\Contracts\Listener;

class WriteRule extends Listener
{

    /**
     * @param DeleteRule $event
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
