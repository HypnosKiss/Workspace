<?php

namespace App\UserListeners\Addresses\UpdateAddresses;


use App\UserEvents\Addresses\UpdateAddresses;
use ZhiEq\Contracts\Listener;

class WriteAddresses extends Listener
{

    /**
     * @param UpdateAddresses $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'name', 'phone', 'province_code', 'province_text', 'city_code', 'city_text',
            'county_code', 'county_text', 'address', 'postal_code', 'status'
        ];
        return $event->addressesModel->fillable($fields)->fill(snake_case_array_keys($event->input))
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}