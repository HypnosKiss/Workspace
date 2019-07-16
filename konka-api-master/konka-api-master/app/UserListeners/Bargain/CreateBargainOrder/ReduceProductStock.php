<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;


use App\Models\ProductSpecification;
use App\UserEvents\Order\CreateOrder;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class ReduceProductStock extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return count($event->input['products']) === collect($event->input['products'])->filter(function ($item) {
                if (!$productSpecification = ProductSpecification::whereCode($item['productSpecificationCodes'])->where('stock', '>', 0)->first()) {
                    throw new CustomException('库存不足');
                }
                return $productSpecification->setAttribute('stock', $productSpecification->stock - 1)->save();
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 4;
    }
}
