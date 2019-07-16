<?php

namespace App\UserListeners\ShoppingCart\UpdateShoppingCart;


use App\UserEvents\ShoppingCart\UpdateShoppingCart;
use ZhiEq\Contracts\Listener;

class WriteShoppingCart extends Listener
{

    /**
     * @param UpdateShoppingCart $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->shoppingCartModel->setAttribute('product_specification_code', $event->input['productSpecificationCode'])
            ->setAttribute('num', $event->input['num'])
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