<?php

namespace App\UserListeners\Product\UpdateTags;

use App\Models\ProductTag;
use App\UserEvents\Product\UpdateTags;
use ZhiEq\Contracts\Listener;

class WriteDeleteTags extends Listener
{

    /**
     * @param UpdateTags $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return ProductTag::whereProductCode($event->productModel->code)->whereIn('tag_code', $event->needDeleteTags->toArray())->delete() === $event->needDeleteTags->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
