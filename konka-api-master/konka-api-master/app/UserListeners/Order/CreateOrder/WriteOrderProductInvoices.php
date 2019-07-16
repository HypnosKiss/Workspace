<?php

namespace App\UserListeners\Order\CreateOrder;


use App\Models\OrderInvoice;
use App\Models\UserInvoice;
use App\UserEvents\Order\CreateOrder;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

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
        if (!$invoice = UserInvoice::whereCode($event->input['invoiceCode'])->first()) {
            throw new CustomException('发票抬头不存在');
        }
        $fields = [
            'invoice_type', 'material_type', 'type', 'unit_name', 'tax_ticket', 'tax_ticket_address', 'tax_ticket_phone',
            'open_bank', 'bank_account', 'name', 'phone', 'province', 'city', 'county', 'address', 'send_email', 'send_mobile'
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
