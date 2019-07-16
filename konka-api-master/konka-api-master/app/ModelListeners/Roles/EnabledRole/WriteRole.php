<?php

namespace App\ModelListeners\Roles\EnabledRole;

use App\ModelEvents\Roles\EnabledRole;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteRole extends Listener
{

    /**
     * @param EnabledRole $event
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
