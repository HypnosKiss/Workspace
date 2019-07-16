<?php

namespace App\UserListeners\Administrator\UpdateAdministrator;


use App\UserEvents\Administrator\UpdateAdministrator;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteAdministrator extends Listener
{

    /**
     * @param UpdateAdministrator $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->adminModel
            ->fillable(['username', 'nickname', 'type'])
            ->fill($event->input)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
