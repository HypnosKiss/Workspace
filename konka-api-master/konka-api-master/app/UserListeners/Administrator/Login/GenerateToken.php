<?php

namespace App\UserListeners\Administrator\Login;


use App\UserEvents\Administrator\Login;
use ZhiEq\ApiTokenAuth\Facades\ApiToken;
use ZhiEq\ApiTokenAuth\Token;
use ZhiEq\Contracts\Listener;

class GenerateToken extends Listener
{

    /**
     * @param Login $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $this->apiToken()->setUserCode($event->adminModel->code);
        return ['accessToken' => $this->apiToken()->getToken()];
    }

    /**
     * @return Token
     */

    protected function apiToken()
    {
        return ApiToken::get('admin');
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
