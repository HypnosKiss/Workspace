<?php

namespace App\UserEvents\Order;


use App\Models\Order;
use ZhiEq\Exceptions\CustomException;

class UpdateTrackingNumber
{
    /**
     * @var string $trackingNumber
     */

    public $trackingNumber;

    /**
     * @var Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $orderModel;

    /**
     * UpdateTrackingNumber constructor.
     * @param $input
     * @param $code
     */

    public function __construct($input, $code)
    {
        if (empty($input['trackingNumber'])) {
            throw new CustomException('快递单号不能为空');
        }
        if (!$this->orderModel = Order::whereCode($code)->first()) {
            throw new CustomException('编码无效');
        }
        $this->trackingNumber = $input['trackingNumber'];
    }
}