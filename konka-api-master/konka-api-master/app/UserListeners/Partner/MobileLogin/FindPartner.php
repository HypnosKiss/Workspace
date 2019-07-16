<?php

namespace App\UserListeners\Partner\MobileLogin;

use App\Models\Partner;
use App\UserEvents\Partner\MobileLogin;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class FindPartner extends Listener
{

    /**
     * @param MobileLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->model = Partner::whereClientPhone($event->input['mobile'])->first()) {
            throw new CustomException('未注册的合伙人');
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
