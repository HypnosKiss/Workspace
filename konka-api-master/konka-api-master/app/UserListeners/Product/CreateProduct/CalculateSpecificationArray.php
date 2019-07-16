<?php

namespace App\UserListeners\Product\CreateProduct;

use App\Models\Specification;
use App\UserEvents\Product\CreateProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class CalculateSpecificationArray extends Listener
{

    /**
     * @param CreateProduct $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $event->specifications = Specification::whereStatus(Specification::STATUS_ENABLE)->whereIn('code', $event->input['specification'])->get();
        if (count($event->input['specification']) !== $event->specifications->count()) {
            throw new CustomException('存在无效的产品规格，请重新选择产品规格');
        }
        $categoryList = [];
        foreach ($event->specifications as $specification) {
            if (!in_array($specification['parent_code'], $categoryList)) {
                $categoryList[] = $specification['parent_code'];
            }
        }
        $event->specificationCategory = Specification::whereStatus(Specification::STATUS_ENABLE)->whereIn('code', $categoryList)->get();
        $event->specificationArray = $event->specificationCategory->map(function ($category) use ($event) {
            return [
                'code' => $category['code'],
                'name' => $category['name'],
                'sub_specification' => $event->specifications->where('parent_code', $category['code'])->map(function ($sub) use ($category) {
                    return [
                        'code' => $sub['code'],
                        'name' => $sub['name'],
                        'parent_code' => $category['code']
                    ];
                })->values()->toArray()
            ];
        })->toArray();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
