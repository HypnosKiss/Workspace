<?php

namespace App\UserListeners\BaseInfo\SaveSetting;


use App\Models\BaseInfo;
use App\UserEvents\BaseInfo\SaveSetting;
use ZhiEq\Contracts\Listener;

class WriteSetting extends Listener
{

    /**
     * @param SaveSetting $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return collect($event->input['settings'])->count() === collect($event->input['settings'])->filter(function ($item, $index) {
                if (!in_array($index, BaseInfo::getTypeList())) {
                    return true;
                }
                return BaseInfo::setSetting($index, $item);
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}