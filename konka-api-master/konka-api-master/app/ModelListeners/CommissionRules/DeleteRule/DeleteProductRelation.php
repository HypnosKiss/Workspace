<?php

namespace App\ModelListeners\CommissionRules\DeleteRule;

use App\ModelEvents\CommissionRules\DeleteRule;
use App\Models\CommissionRuleProductRelation;
use ZhiEq\Contracts\Listener;

class DeleteProductRelation extends Listener
{

    /**
     * @param DeleteRule $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return CommissionRuleProductRelation::whereCommissionRuleCode($event->model->code)->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
