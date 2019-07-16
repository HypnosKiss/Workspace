<?php

namespace App\UserListeners\Partner\WxPhoneLogin;

use App\Models\WxAppletsSession;
use App\UserEvents\Partner\WxPhoneLogin;
use Carbon\Carbon;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class DecodeWxPhone extends Listener
{

    /**
     * @param WxPhoneLogin $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        if (!$sessionKey = WxAppletsSession::getSessionKey()) {
            throw new CustomException('会话已超时，请重试', 50088);
        }
        $data = app('wechat.mini_program')->encryptor->decryptData($sessionKey, $event->input['iv'], $event->input['encryptData']);
        if (empty($data)) {
            throw new CustomException('获取微信手机号码失败');
        }
        if ($data['watermark']['appid'] !== WxAppletsSession::getAppId()) {
            throw new CustomException('水印校验失败，请联系管理员');
        }
        $beginTime = Carbon::now()->addMinutes(-5)->timestamp;
        $endTime = Carbon::now()->addMinutes(5)->timestamp;
        if ($data['watermark']['timestamp'] > $endTime || $data['watermark']['timestamp'] < $beginTime) {
            throw new CustomException('非法请求，请检查时间是否正确');
        }
        logs()->info('decode wx phone data', $data);
        $event->mobile = $data['phoneNumber'];
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
