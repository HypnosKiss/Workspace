<?php

namespace App\UserListeners\Product\UpdateProduct;

use App\Models\ProductImage;
use ZhiEq\Contracts\Listener;

class WriteContentImage extends Listener
{

    /**
     * @param $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $nowImageList = ProductImage::whereType(ProductImage::TYPE_CONTENT)->whereProductCode($event->productModel->code)->get();
        return count($event->input['content']) === collect($event->input['content'])->filter(function ($image, $index) use ($nowImageList, $event) {
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
                        ->setAttribute('type', ProductImage::TYPE_CONTENT)
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
        return 4;
    }
}
