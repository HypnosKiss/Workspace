<?php

namespace App\UserListeners\User\WxOpenidLogin;


use App\Models\User;
use App\Models\UserOauth;
use App\UserEvents\User\WxOpenidLogin;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class BindOpenid extends Listener
{

    /**
     * @param WxOpenidLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        \Log::info('Start Get Open Id');
        if (!$userOauth = UserOauth::whereType(UserOauth::TYPE_WX_APPLETS)->whereOpenId($event->input['openId'])->first()) {
            $user = new User();
            if (!$user->save()) {
                throw new CustomException('创建用户失败');
            }
            $userOauth = new UserOauth();
            if (!$userOauth->setAttribute('type', UserOauth::TYPE_WX_APPLETS)
                ->setAttribute('open_id', $event->input['openId'])
                ->setAttribute('user_code', $user->code)
                ->save()) {
                throw new CustomException('创建第三方登录失败');
            }
        }
        $event->user = $userOauth->user;
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
