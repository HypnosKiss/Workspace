<?php

namespace App\ModelListeners\UserInvoice\DeleteInvoice;

use App\ModelEvents\UserInvoice\DeleteInvoice;
use App\Models\PublicDefinition;
use App\Models\UserInvoice;
use ZhiEq\Contracts\Listener;

class UpdateDefault extends Listener
{

    /**
     * @param DeleteInvoice $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if ($event->model->is_default === PublicDefinition::SWITCH_YES) {
            if ($model = UserInvoice::whereUserCode($event->model->user_code)->where('code', '!=', $event->model->code)->first()) {
                return $model->setAttribute('id_default', PublicDefinition::SWITCH_YES)->save();
            }
        }
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
