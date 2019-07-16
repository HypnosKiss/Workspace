<?php

namespace App\ModelListeners\Tags\DisableTag;

use App\ModelEvents\Tags\DisableTag;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteTag extends Listener
{

    /**
     * @param DisableTag $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->tagModel->setAttribute('status', PublicDefinition::STATUS_DISABLED)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
