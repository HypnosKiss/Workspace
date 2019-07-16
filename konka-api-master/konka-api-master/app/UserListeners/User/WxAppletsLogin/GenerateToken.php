<?php

namespace App\UserListeners\User\WxAppletsLogin;

use App\UserEvents\User\WxAppletsLogin;
use ZhiEq\ApiTokenAuth\Facades\ApiToken;
use ZhiEq\ApiTokenAuth\Token;
use ZhiEq\Contracts\Listener;

class GenerateToken extends Listener
{

    /**
     * @param WxAppletsLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $this->apiToken()->setUserCode($event->user->code);
        return ['accessToken' => $this->apiToken()->getToken()];
    }

    /**
     * @return Token
     */

    protected function apiToken()
    {
        return ApiToken::get();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 4;
    }
}
