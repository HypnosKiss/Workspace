<?php

namespace App\ModelListeners\Partner\DeletePartner;

use App\ModelEvents\Partner\DeletePartner;
use App\Models\Partner;
use ZhiEq\Contracts\Listener;

class ClearSubPartner extends Listener
{

    /**
     * @param DeletePartner $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return Partner::whereParentCode($event->model->code)->update(['parent_code' => null]) !== false;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
