<?php

namespace App\UserListeners\User\WxAppletsLogin;


use App\Models\UserOauth;
use App\Models\WxAppletsSession;
use App\UserEvents\User\WxAppletsLogin;
use ZhiEq\Contracts\Listener;

class BindOpenid extends Listener
{

    /**
     * @param WxAppletsLogin $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        if (!$openid = WxAppletsSession::getOpenid()) {
            return true;
        }
        if (UserOauth::whereOpenId($openid)->whereType(UserOauth::TYPE_WX_APPLETS)->exists()) {
            return true;
        }
        if (!$oauth = UserOauth::whereUserCode($event->user->code)->whereType(UserOauth::TYPE_WX_APPLETS)->first()) {
            $oauth = (new UserOauth())->setAttribute('type', UserOauth::TYPE_WX_APPLETS)
                ->setAttribute('user_code', $event->user->code);
        }
        if (!empty($oauth->openid)) {
            return true;
        }
        return $oauth->setAttribute('open_id', $openid)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 2;
    }
}
