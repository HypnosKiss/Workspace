<?php

namespace App\ModelListeners\Roles\EnabledRole;

use App\ModelEvents\Roles\EnabledRole;
use App\Models\AdminRole;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteAdminRole extends Listener
{

    /**
     * @param EnabledRole $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return AdminRole::whereRoleCode($event->model->code)->update(['status' => PublicDefinition::STATUS_ENABLED]);
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
