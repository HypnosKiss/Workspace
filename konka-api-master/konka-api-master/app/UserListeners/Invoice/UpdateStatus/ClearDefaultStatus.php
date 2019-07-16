<?php

namespace App\UserListeners\Invoice\UpdateStatus;


use App\Models\PublicDefinition;
use App\Models\UserInvoice;
use App\UserEvents\Invoice\UpdateStatus;
use ZhiEq\Contracts\Listener;

class ClearDefaultStatus extends Listener
{

    /**
     * @param UpdateStatus $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$addresses = UserInvoice::whereUserCode(auth_user()->code)->where('code', '<>', $event->invoiceModel->code)
            ->where('is_default', PublicDefinition::SWITCH_YES)->first()) {
            return true;
        }
        return $addresses->setAttribute('is_default', PublicDefinition::SWITCH_NO)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
