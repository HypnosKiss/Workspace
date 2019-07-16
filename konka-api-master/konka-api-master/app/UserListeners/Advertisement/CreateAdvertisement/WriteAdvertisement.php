<?php

namespace App\UserListeners\Advertisement\CreateAdvertisement;


use App\Models\Advertisement;
use App\UserEvents\Advertisement\CreateAdvertisement;
use ZhiEq\Contracts\Listener;

class WriteAdvertisement extends Listener
{

    /**
     * @param CreateAdvertisement $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'title', 'content', 'order', 'connect', 'connect_type'
        ];
        return (new Advertisement())->fillable($fields)->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()))
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
