<?php

namespace App\ModelListeners\Admin\DisabledAdmin;

use App\ModelEvents\Admin\DisabledAdmin;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteAdmin extends Listener
{

    /**
     * @param DisabledAdmin $event
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
