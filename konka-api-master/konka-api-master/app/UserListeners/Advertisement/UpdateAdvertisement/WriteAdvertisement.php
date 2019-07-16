<?php

namespace App\UserListeners\Advertisement\UpdateAdvertisement;


use App\Models\Advertisement;
use App\UserEvents\Advertisement\UpdateAdvertisement;
use ZhiEq\Contracts\Listener;

class WriteAdvertisement extends Listener
{

    /**
     * @param UpdateAdvertisement $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'title', 'content', 'extend', 'order', 'start_at', 'end_at', 'connect', 'connect_type', 'type'
        ];
        return $event->advertisementModel->fillable($fields)->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()))
            ->setAttribute('position', Advertisement::POSITION_INDEX)
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