<?php

namespace App\UserListeners\Product\UpdateProduct;


use App\Models\ProductImage;
use App\UserEvents\Product\UpdateProduct;
use ZhiEq\Contracts\Listener;

class WriteMainImage extends Listener
{

    /**
     * @param UpdateProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if ($image = ProductImage::whereType(ProductImage::TYPE_THUMB)
            ->whereProductCode($event->productModel->code)
            ->first()) {
            return $image->setAttribute('image', $event->input['mainImage'])
                ->save();
        }
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
