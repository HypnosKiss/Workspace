<?php

namespace App\UserListeners\Addresses\CreateAddresses;


use App\Models\UserAddresses;
use App\UserEvents\Addresses\CreateAddresses;
use ZhiEq\Contracts\Listener;

class WriteAddresses extends Listener
{

    /**
     * @param CreateAddresses $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'name', 'phone', 'province_code', 'province_text', 'city_code', 'city_text',
            'county_code', 'county_text', 'address', 'postal_code', 'status'
        ];
        return (new UserAddresses())->fillable($fields)->fill(snake_case_array_keys($event->input))
            ->setAttribute('user_code', auth_user()->code)
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