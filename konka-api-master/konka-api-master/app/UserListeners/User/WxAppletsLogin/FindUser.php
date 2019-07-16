<?php

namespace App\UserListeners\User\WxAppletsLogin;

use App\Models\User;
use App\UserEvents\User\WxAppletsLogin;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class FindUser extends Listener
{

    /**
     * @param WxAppletsLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->user = User::whereUsername($event->mobile)->first()) {
            $user = new User();
            if (!$user->setAttribute('username', $event->mobile)->save()) {
                throw new CustomException('创建用户失败');
            }
            $event->user = $user;
        }
        return true;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
