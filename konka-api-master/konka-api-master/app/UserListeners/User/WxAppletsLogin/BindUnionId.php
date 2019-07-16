<?php

namespace App\UserListeners\User\WxAppletsLogin;

use App\Models\UserOauth;
use App\Models\WxAppletsSession;
use App\UserEvents\User\WxAppletsLogin;
use ZhiEq\Contracts\Listener;

class BindUnionId extends Listener
{

    /**
     * @param WxAppletsLogin $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        if (!$openid = WxAppletsSession::getUnionId()) {
            return true;
        }
        if (UserOauth::whereOpenid($openid)->whereType(UserOauth::TYPE_WX_UNION_ID)->exists()) {
            return true;
        }
        if (!$oauth = UserOauth::whereUserCode($event->user->code)->whereType(UserOauth::TYPE_WX_UNION_ID)->first()) {
            $oauth = (new UserOauth())->setAttribute('type', UserOauth::TYPE_WX_UNION_ID)
                ->setAttribute('user_code', $event->user->code);
        }
        if (!empty($oauth->openid)) {
            return true;
        }
        return $oauth->setAttribute('openid', $openid)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 3;
    }
}
