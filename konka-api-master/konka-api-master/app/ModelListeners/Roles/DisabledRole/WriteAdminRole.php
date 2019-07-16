<?php

namespace App\ModelListeners\Roles\DisabledRole;

use App\ModelEvents\Roles\DisabledRole;
use App\Models\AdminRole;
use App\Models\PublicDefinition;
use ZhiEq\Contracts\Listener;

class WriteAdminRole extends Listener
{

    /**
     * @param DisabledRole $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return AdminRole::whereRoleCode($event->model->code)->update(['status' => PublicDefinition::STATUS_DISABLED]);
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
