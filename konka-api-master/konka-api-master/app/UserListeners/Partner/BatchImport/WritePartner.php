<?php

namespace App\UserListeners\Partner\BatchImport;


use App\Models\Partner;
use App\UserEvents\Partner\BatchImport;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WritePartner extends Listener
{

    /**
     * @param BatchImport $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return count($event->input) === count(array_filter($event->input, function ($input) {
                return (new Partner())
                    ->fillable([
                        'type', 'area', 'company_name', 'r3_code',
                        'client_name', 'merge_code', 'client_type',
                        'company_address', 'salesman', 'salesman_phone',
                        'client_phone', 'inline_name', 'inline_number',
                        'first_department', 'second_department', 'third_department',
                        'handing_name', 'network_name', 'network_code',
                        'parent_client_name', 'parent_client_code',
                        'province', 'city', 'county', 'town', 'activation_code'
                    ])
                    ->fill(snake_case_array_keys($input))
                    ->save();
            }));
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
