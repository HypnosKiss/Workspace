<?php

namespace App\UserListeners\Partner\WxPhoneLogin;

use App\Models\Partner;
use App\UserEvents\Partner\WxPhoneLogin;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class FindPartner extends Listener
{

    /**
     * @param WxPhoneLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->model = Partner::whereClientPhone($event->mobile)->first()) {
            throw new CustomException('未注册的合伙人');
        }
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
