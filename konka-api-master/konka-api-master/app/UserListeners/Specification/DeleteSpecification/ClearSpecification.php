<?php

namespace App\UserListeners\Specification\DeleteSpecification;


use App\UserEvents\Specification\DeleteSpecification;
use ZhiEq\Contracts\Listener;

class ClearSpecification extends Listener
{

    /**
     * @param DeleteSpecification $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return $event->specificationModel->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}