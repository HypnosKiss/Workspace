<?php

namespace App\ModelListeners\Partner\DowngradePartner;

use App\ModelEvents\Partner\DowngradePartner;
use App\Models\Partner;
use ZhiEq\Contracts\Listener;

class WritePartner extends Listener
{

    /**
     * @param DowngradePartner $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->model->setAttribute('type', Partner::TYPE_EXTERNAL)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
