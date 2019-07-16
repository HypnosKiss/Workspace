<?php

namespace App\ModelListeners\Partner\EnabledPartner;

use App\ModelEvents\Partner\EnabledPartner;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WritePartner extends Listener
{

    /**
     * @param EnabledPartner $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->model->setAttribute('status', PublicDefinition::STATUS_ENABLED)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
