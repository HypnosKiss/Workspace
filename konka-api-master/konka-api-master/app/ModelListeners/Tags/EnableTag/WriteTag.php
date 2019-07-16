<?php

namespace App\ModelListeners\Tags\EnableTag;

use App\ModelEvents\Tags\EnableTag;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteTag extends Listener
{

    /**
     * @param EnableTag $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->tagModel->setAttribute('status', PublicDefinition::STATUS_ENABLED)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
