<?php

namespace App\ModelListeners\Partner\DisabledPartner;

use App\ModelEvents\Partner\DisabledPartner;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WritePartner extends Listener
{

    /**
     * @param DisabledPartner $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->model->setAttribute('status', PublicDefinition::STATUS_DISABLED)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
