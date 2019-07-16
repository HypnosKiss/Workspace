<?php

namespace App\ModelListeners\Admin\EnabledAdmin;

use App\ModelEvents\Admin\EnabledAdmin;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteAdmin extends Listener
{

    /**
     * @param EnabledAdmin $event
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
