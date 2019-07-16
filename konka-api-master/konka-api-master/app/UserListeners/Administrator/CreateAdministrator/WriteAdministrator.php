<?php

namespace App\UserListeners\Administrator\CreateAdministrator;


use App\Models\Admin;
use App\UserEvents\Administrator\CreateAdministrator;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteAdministrator extends Listener
{

    /**
     * @param CreateAdministrator $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $event->adminModel = new Admin();
        return $event->adminModel
            ->fillable(['username', 'nickname', 'type'])
            ->fill($event->input)
            ->setAttribute('password', \Hash::make($event->input['password']))
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
