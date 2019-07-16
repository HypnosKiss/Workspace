<?php

namespace App\UserListeners\User\UpdateInfoAvatar;


use App\Models\WxAppletsSession;
use App\UserEvents\User\UpdateInfoAvatar;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteAvatar extends Listener
{

    /**
     * @param UpdateInfoAvatar $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        if (!$sessionKey = WxAppletsSession::getSessionKey()) {
            throw new CustomException('会话已超时，请重试', 50088);
        }
        $data = app('wechat.mini_program')->encryptor->decryptData($sessionKey, $event->iv, $event->encryptData);
        if (empty($data)) {
            throw new CustomException('获取微信信息失败');
        }
        return auth_user()->setAttribute('nickname', $data['nickName'])->setAttribute('avatar', $data['avatarUrl'])->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
