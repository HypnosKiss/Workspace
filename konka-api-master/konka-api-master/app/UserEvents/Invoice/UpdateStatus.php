<?php

namespace App\UserEvents\Invoice;


use App\Models\UserInvoice;
use ZhiEq\Exceptions\CustomException;

class UpdateStatus
{
    /**
     * @var UserInvoice|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $invoiceModel;

    /**
     * UpdateStatus constructor.
     * @param $code
     */

    public function __construct($code)
    {
        if (!$this->invoiceModel = UserInvoice::whereCode($code)->first()) {
            throw new CustomException('编码无效');
        }
    }
}