<?php

namespace App\UserListeners\VerificationCode\SendSmsCode;

use App\Extend\KonKaWebservice;
use App\UserEvents\VerificationCode\SendSmsCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use ZhiEq\Contracts\Listener;

class SendSms extends Listener implements ShouldQueue
{

    /**
     * @param SendSmsCode $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $result = KonKaWebservice::postSms($event->input['mobile'], $event->code);
        logs()->info('send sms result:', ['result' => $result]);
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
