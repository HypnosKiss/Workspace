<?php

namespace App\UserListeners\User\WxOpenidLogin;

use App\UserEvents\User\WxOpenidLogin;
use ZhiEq\ApiTokenAuth\Facades\ApiToken;
use ZhiEq\ApiTokenAuth\Token;
use ZhiEq\Contracts\Listener;

class GenerateToken extends Listener
{

    /**
     * @param WxOpenidLogin $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        \Log::info('Get Access Token');
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
        return 1;
    }
}
