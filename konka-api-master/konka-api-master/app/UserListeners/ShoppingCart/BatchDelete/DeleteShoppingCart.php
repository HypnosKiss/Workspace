<?php

namespace App\UserListeners\ShoppingCart\BatchDelete;


use App\Models\ShoppingCart;
use App\UserEvents\ShoppingCart\BatchDelete;
use ZhiEq\Contracts\Listener;

class DeleteShoppingCart extends Listener
{

    /**
     * @param BatchDelete $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return count($event->input['ids']) === collect($event->input['ids'])->map(function ($item) {
                if (!$shoppingCart = ShoppingCart::whereId($item)->whereUserCode(auth_user()->code)->first()) {
                    logs()->info('Shopping Cart No Exists');
                    return false;
                }
                return $shoppingCart->delete();
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}