<?php

namespace App\UserEvents\Order;


use App\Models\Order;
use Carbon\Carbon;

class PayOrder
{
    /**
     * @var Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */

    public $order;

    /**
     * @var Carbon
     */

    public $payedAt;

    /**
     * @var null
     */

    public $payNumber;

    /**
     * PayOrder constructor.
     * @param Order $model
     * @param null $payedAt
     * @param null $payNumber
     */

    public function __construct(Order $model, $payedAt = null, $payNumber = null)
    {
        $this->order = $model;
        $this->payedAt = $payedAt === null ? Carbon::now() : new Carbon($payedAt);
        $this->payNumber = $payNumber;
    }
}
