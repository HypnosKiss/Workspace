<?php

namespace App\UserListeners\Partner\ResetPassword;


use App\Models\Partner;
use App\UserEvents\Partner\ResetPassword;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class FindPartner extends Listener
{

    /**
     * @param ResetPassword $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$event->partner = Partner::whereClientPhone($event->input['mobile'])->first()) {
            throw new CustomException('不存在的合伙人档案');
        }
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
