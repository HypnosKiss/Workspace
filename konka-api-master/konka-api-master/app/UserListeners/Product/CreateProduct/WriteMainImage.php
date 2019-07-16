<?php

namespace App\UserListeners\Product\CreateProduct;

use App\Models\ProductImage;
use App\UserEvents\Product\CreateProduct;
use ZhiEq\Contracts\Listener;

class WriteMainImage extends Listener
{

    /**
     * @param CreateProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return (new ProductImage())->setAttribute('product_code', $event->productModel->code)
            ->setAttribute('image', $event->input['mainImage'])
            ->setAttribute('order', 0)
            ->setAttribute('type', ProductImage::TYPE_THUMB)
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 2;
    }
}
