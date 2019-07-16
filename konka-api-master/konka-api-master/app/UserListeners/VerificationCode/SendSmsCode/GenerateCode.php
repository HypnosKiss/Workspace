<?php

namespace App\UserListeners\VerificationCode\SendSmsCode;


use App\UserEvents\VerificationCode\SendSmsCode;
use ZhiEq\Contracts\Listener;

class GenerateCode extends Listener
{

    /**
     * @param SendSmsCode $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        list($event->codeId, $event->code) = app('verification_code')->generateAndSave($event->input['mobile']);
        return ['codeId' => $event->codeId];
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
