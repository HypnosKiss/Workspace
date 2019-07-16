<?php

namespace App\UserListeners\Partner\WxPhoneLogin;

use App\UserEvents\Partner\WxPhoneLogin;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class CheckBindRelation extends Listener
{

    /**
     * @param WxPhoneLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!empty(auth_user()->partner_code) && $event->model->code !== auth_user()->partner_code) {
            throw new CustomException('你已登录其他合伙人，请先退出登录');
        }
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 2;
    }
}
