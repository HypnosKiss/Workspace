<?php

namespace App\ModelEvents\UserInvoice;

use App\Models\UserInvoice;

class DeleteInvoice
{
    /**
     * @var UserInvoice
     */

    public $model;

    /**
     * DeleteInvoice constructor.
     * @param UserInvoice $model
     */

    public function __construct(UserInvoice $model)
    {
        $this->model = $model;
    }
}
