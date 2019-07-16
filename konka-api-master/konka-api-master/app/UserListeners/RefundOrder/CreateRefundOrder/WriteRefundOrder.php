<?php

namespace App\UserListeners\RefundOrder\CreateRefundOrder;


use App\Models\RefundOrder;
use App\UserEvents\RefundOrder\CreateRefundOrder;
use ZhiEq\Contracts\Listener;

class WriteRefundOrder extends Listener
{

    /**
     * @param CreateRefundOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $event->refundOrderModel = new RefundOrder();
        $fields = [
            'type', 'refund_type', 'content', 'images', 'num'
        ];
        $event->refundOrderModel->fillable($fields)
            ->fill(snake_case_array_keys($event->input));
        return $event->refundOrderModel
            ->setAttribute('user_code', auth_user()->code)
            ->setAttribute('order_code', $event->orderModel->code)
            ->setAttribute('product_code', $event->productModel->product_code)
            ->setAttribute('product_title', $event->productModel->name)
            ->setAttribute('product_sub_title', $event->productModel->sub_title)
            ->setAttribute('product_image', $event->productModel->image)
            ->setAttribute('product_price', $event->productModel->price)
            ->setAttribute('product_specification_text', $event->productModel->specifications_text)
            ->setAttribute('price', round($event->productModel->price * $event->input['num'], 2))
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
