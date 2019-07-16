<?php

namespace App\ModelListeners\CommissionRules\DisabledRule;

use App\ModelEvents\CommissionRules\DisabledRule;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteRule extends Listener
{

    /**
     * @param DisabledRule $event
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
