<?php

namespace App\UserListeners\Product\UpdateProduct;

use App\Models\ShoppingCart;
use App\UserEvents\Product\UpdateProduct;
use ZhiEq\Contracts\Listener;

class ClearShoppingCar extends Listener
{

    /**
     * @param UpdateProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return collect($event->deleteSpecification)->count() === collect($event->deleteSpecification)->filter(function ($specificationCode) {
                return ShoppingCart::whereProductSpecificationCode($specificationCode)->delete() !== false;
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 6;
    }
}
