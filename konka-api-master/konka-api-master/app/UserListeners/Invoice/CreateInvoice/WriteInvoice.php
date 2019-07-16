<?php

namespace App\UserListeners\Invoice\CreateInvoice;


use App\Models\UserAddresses;
use App\Models\UserInvoice;
use App\UserEvents\Invoice\CreateInvoice;
use ZhiEq\Contracts\Listener;

class WriteInvoice extends Listener
{

    /**
     * @param CreateInvoice $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'invoice_type', 'type', 'unit_name', 'tax_ticket', 'tax_ticket_address',
            'tax_ticket_phone', 'open_bank', 'bank_account', 'material_type', 'name', 'phone',
            'province', 'city', 'county', 'address', 'send_email', 'send_mobile'
        ];
        return (new UserInvoice())
            ->fillable($fields)
            ->fill(snake_case_array_keys($event->input))
            ->setAttribute('user_code', auth_user()->code)
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
