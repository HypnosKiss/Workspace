<?php

namespace App\UserListeners\Administrator\Login;


use App\Models\Admin;
use App\UserEvents\Administrator\Login;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class FindAdmin extends Listener
{

    /**
     * @param Login $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->adminModel = Admin::enabled()->whereUsername($event->input['username'])->first()) {
            throw new CustomException('用户名不存在');
        }
        if (!$event->adminModel->validatePassword($event->input['password'])) {
            throw new CustomException('密码错误');
        }
        return true;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
