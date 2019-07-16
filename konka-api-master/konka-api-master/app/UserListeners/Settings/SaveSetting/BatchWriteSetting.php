<?php

namespace App\UserListeners\Settings\SaveSetting;

use App\Models\Setting;
use App\UserEvents\Settings\SaveSetting;
use ZhiEq\Contracts\Listener;

class BatchWriteSetting extends Listener
{

    /**
     * @param SaveSetting $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return count($event->input) === collect($event->input)->filter(function ($value, $key) {
                return Setting::setValue($key, $value);
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
