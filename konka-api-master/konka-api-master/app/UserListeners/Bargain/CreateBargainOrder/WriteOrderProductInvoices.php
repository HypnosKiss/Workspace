<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;

use App\Models\OrderInvoice;
use App\Models\UserInvoice;
use App\UserEvents\Order\CreateOrder;
use ZhiEq\Contracts\Listener;

class WriteOrderProductInvoices extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (empty($event->input['invoiceCode'])) {
            return true;
        }
        $invoice = UserInvoice::whereCode($event->input['invoiceCode'])->first();
        $fields = [
            'invoice_type', 'type', 'unit_name', 'tax_ticket', 'tax_ticket_address', 'tax_ticket_phone',
            'open_bank', 'bank_account', 'name', 'phone', 'province_code', 'province_text', 'city_code',
            'city_text', 'county_code', 'county_text', 'address'
        ];
        return (new OrderInvoice())->fillable($fields)->fill($invoice->only($fields))
            ->setAttribute('order_code', $event->orderModel->code)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 2;
    }
}
