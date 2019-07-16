<?php

namespace App\ModelListeners\CommissionRules\EnabledRule;


use App\ModelEvents\CommissionRules\EnabledRule;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteRule extends Listener
{

    /**
     * @param EnabledRule $event
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
