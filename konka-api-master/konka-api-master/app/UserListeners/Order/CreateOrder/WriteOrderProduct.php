<?php

namespace App\UserListeners\Order\CreateOrder;


use App\Models\OrderProduct;
use App\Models\ProductSpecification;
use App\UserEvents\Order\CreateOrder;
use Log;
use ZhiEq\Contracts\Listener;

class WriteOrderProduct extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return count($event->input['products']) === collect($event->input['products'])->filter(function ($item) use ($event) {
                if (!$productSpecification = ProductSpecification::whereCode($item['productSpecificationCodes'])->first()) {
                    Log::info('Create Order Product Specification Exception');
                    return false;
                }
                $orderProduct = new OrderProduct();
                if (!$orderProduct->setAttribute('order_code', $event->orderModel->code)
                    ->setAttribute('product_code', $productSpecification->product_code)
                    ->setAttribute('product_specifications_code', $productSpecification->code)
                    ->setAttribute('name', $productSpecification->product->title)
                    ->setAttribute('sub_title', $productSpecification->product->sub_title)
                    ->setAttribute('image', empty($productSpecification->image) ? $productSpecification->product->main_image : $productSpecification->image)
                    ->setAttribute('specifications', $this->specificationText($productSpecification->product->specification_array,
                        $productSpecification->specification_codes))
                    ->setAttribute('price', $productSpecification->price)
                    ->setAttribute('num', $item['num'])
                    ->save()) {
                    Log::info('Create Order Product Failure');
                    return false;
                }
                $event->orderProduct[] = $orderProduct;
                return true;
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }

    /**
     * 规格编码转换成数组（含对应中文）
     *
     * @param $specificationArray
     * @param $codes
     * @return mixed
     */

    protected function specificationText($specificationArray, $codes)
    {
        /*$sub = [];
        $parentSpecification = collect($specificationArray)->map(function ($item) use (&$sub) {
            if (!empty($item['sub_specification'])) {
                foreach ($item['sub_specification'] as $index => $subSpecification) {
                    $sub[] = $subSpecification;
                }
            }
            unset($item['sub_specification']);
            return $item;
        });
        return collect($parentSpecification)->whereIn('code', $codes)->map(function ($item) use ($sub, $codes) {
            $item['sub_specification'] = collect($sub)->whereIn('code', $codes)->first();
            return $item;
        })->toArray();*/
        $sub = collect($specificationArray)->pluck('sub_specification')->collapse()->whereIn('code', $codes);
        return collect($specificationArray)->map(function ($item) use ($sub) {
            $item['sub_specification'] = collect($sub)->where('parent_code', $item['code'])->values()->toArray();
            return $item;
        })->values()->toArray();
    }
}
