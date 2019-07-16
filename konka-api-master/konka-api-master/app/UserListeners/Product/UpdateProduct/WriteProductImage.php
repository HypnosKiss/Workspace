<?php

namespace App\UserListeners\Product\UpdateProduct;


use App\Models\ProductImage;
use App\UserEvents\Product\UpdateProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteProductImage extends Listener
{

    /**
     * @param UpdateProduct $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        $nowImageList = ProductImage::whereType(ProductImage::TYPE_IMAGE)->whereProductCode($event->productModel->code)->get();
        return count($event->input['images']) === collect($event->input['images'])->filter(function ($image, $index) use ($nowImageList, $event) {
                $nowImage = $nowImageList->where('order', $index)->first();
                /**
                 * @var ProductImage $nowImage
                 */
                if (empty($nowImage) && empty($image['image'])) {
                    return true;
                } elseif (empty($nowImage) && !empty($image['image'])) {
                    return (new ProductImage())->setAttribute('product_code', $event->productModel->code)
                        ->setAttribute('image', $image['image'])
                        ->setAttribute('order', $index)
                        ->setAttribute('type', ProductImage::TYPE_IMAGE)
                        ->save();
                } elseif (!empty($nowImage) && empty($image['image'])) {
                    return $nowImage->delete();
                } else {
                    return $nowImage->setAttribute('image', $image['image'])->save();
                }
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
