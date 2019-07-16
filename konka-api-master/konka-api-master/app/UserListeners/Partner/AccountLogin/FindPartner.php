<?php

namespace App\UserListeners\Partner\AccountLogin;

use App\Models\Partner;
use App\UserEvents\Partner\AccountLogin;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class FindPartner extends Listener
{

    /**
     * @param AccountLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->model = Partner::whereClientPhone($event->input['username'])->first()) {
            throw new CustomException('账号密码不正确');
        }
        if (!$event->model->validatePassword($event->input['password'])) {
            throw new CustomException('账号密码不正确');
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
