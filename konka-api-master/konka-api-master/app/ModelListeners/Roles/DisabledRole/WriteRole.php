<?php

namespace App\ModelListeners\Roles\DisabledRole;

use App\ModelEvents\Roles\DisabledRole;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteRole extends Listener
{

    /**
     * @param DisabledRole $event
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
