<?php

namespace App\UserListeners\Evaluation\CreateEvaluationReply;


use App\Models\EvaluationReply;
use App\UserEvents\Evaluation\CreateEvaluationReply;
use ZhiEq\Contracts\Listener;

class WriteEvaluationReply extends Listener
{

    /**
     * @param CreateEvaluationReply $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return (new EvaluationReply())->setAttribute('type', EvaluationReply::TYPE_USER)
            ->setAttribute('evaluation_code', $event->evaluationModel->code)
            ->setAttribute('content', $event->input['content'])
            ->setAttribute('create_person_code', auth_user()->code)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}