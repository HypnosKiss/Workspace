<?php

namespace App\UserListeners\Invoice\UpdateStatus;

use App\Models\PublicDefinition;
use App\UserEvents\Invoice\UpdateStatus;
use ZhiEq\Contracts\Listener;

class UpdateDefaultStatus extends Listener
{

    /**
     * @param UpdateStatus $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->invoiceModel->setAttribute('is_default', PublicDefinition::SWITCH_YES)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
