<?php

namespace App\UserListeners\Evaluation\CreateEvaluation;

use App\UserEvents\Evaluation\CreateEvaluation;
use ZhiEq\Contracts\Listener;

class WriteOrder extends Listener
{

    /**
     * @param  CreateEvaluation $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->orderModel->setAttribute('is_evaluation', 20)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 2;
    }
}
