<?php

namespace App\UserListeners\Partner\ActiveCodeRegister;

use App\Models\Partner;
use App\UserEvents\Partner\ActiveCodeRegister;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class FindPartner extends Listener
{

    /**
     * @param ActiveCodeRegister $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->model = Partner::whereActivationCode($event->input['activeCode'])->first()) {
            throw new CustomException('无效的激活码');
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
