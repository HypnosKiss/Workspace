<?php

namespace App\ModelListeners\CommissionRules\DeleteRule;

use App\ModelEvents\CommissionRules\DeleteRule;
use App\Models\CommissionRulePartnerRelation;
use ZhiEq\Contracts\Listener;

class DeletePartnerRelation extends Listener
{

    /**
     * @param DeleteRule $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return CommissionRulePartnerRelation::whereCommissionRuleCode($event->model->code)->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
