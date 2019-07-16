<?php

namespace App\UserListeners\ProductCategory\DeleteProductCategory;


use App\Models\ProductCategory;
use App\UserEvents\ProductCategory\DeleteProductCategory;
use ZhiEq\Contracts\Listener;

class ClearSubCategory extends Listener
{

    /**
     * @param DeleteProductCategory $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->categoryModel->subCategory->count() === $event->categoryModel->subCategory->filter(function (ProductCategory $item) {
                return $item->delete();
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