<?php

namespace App\UserListeners\Specification\DeleteSpecification;


use App\Models\Specification;
use App\UserEvents\Specification\DeleteSpecification;
use ZhiEq\Contracts\Listener;

class ClearSubSpecification extends Listener
{

    /**
     * @param DeleteSpecification $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->specificationModel->subSpecification->count() === $event->specificationModel->subSpecification->filter(function (Specification $item) {
                return $item->delete();
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