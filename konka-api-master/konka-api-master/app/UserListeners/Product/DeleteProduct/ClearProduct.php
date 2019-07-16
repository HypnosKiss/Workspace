<?php

namespace App\UserListeners\Product\DeleteProduct;


use App\UserEvents\Product\DeleteProduct;
use ZhiEq\Contracts\Listener;

class ClearProduct extends Listener
{

    /**
     * @param DeleteProduct $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return $event->productModel->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 3;
    }
}