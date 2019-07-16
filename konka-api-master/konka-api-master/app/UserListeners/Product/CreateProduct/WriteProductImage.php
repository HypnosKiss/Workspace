<?php

namespace App\UserListeners\Product\CreateProduct;


use App\Models\ProductImage;
use App\UserEvents\Product\CreateProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteProductImage extends Listener
{

    /**
     * @param CreateProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $imageList = collect($event->input['images'])->filter(function ($image) {
            return !empty($image['image']);
        });
        return $imageList->count() === $imageList->filter(function ($image, $index) use ($event) {
                return (new ProductImage())->setAttribute('product_code', $event->productModel->code)
                    ->setAttribute('image', $image['image'])
                    ->setAttribute('order', $index)
                    ->setAttribute('type', ProductImage::TYPE_IMAGE)
                    ->save();
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 3;
    }
}
