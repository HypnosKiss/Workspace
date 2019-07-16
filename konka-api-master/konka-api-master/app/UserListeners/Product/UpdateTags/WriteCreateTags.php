<?php

namespace App\UserListeners\Product\UpdateTags;

use App\Models\ProductTag;
use App\UserEvents\Product\UpdateTags;
use ZhiEq\Contracts\Listener;

class WriteCreateTags extends Listener
{

    /**
     * @param UpdateTags $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->needCreateTags->count() === $event->needCreateTags->filter(function ($tag) use ($event) {
                return (new ProductTag())
                    ->setAttribute('product_code', $event->productModel->code)
                    ->setAttribute('tag_code', $tag)
                    ->save();
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
