<?php

namespace App\UserListeners\ProductCategory\DeleteProductCategory;


use App\UserEvents\ProductCategory\DeleteProductCategory;
use ZhiEq\Contracts\Listener;

class ClearCategory extends Listener
{

    /**
     * @param DeleteProductCategory $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return $event->categoryModel->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}