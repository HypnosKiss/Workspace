<?php

namespace App\UserListeners\Product\UpdateProduct;


use App\Models\ProductSpecification;
use App\UserEvents\Product\UpdateProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteProductSpecification extends Listener
{

    protected $selectMaxBox = [];

    protected $selectBox = [];

    /**
     * @param UpdateProduct $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        $levelNum = count($event->specificationCategory);
        $totalNum = 1;
        $moveLevel = $levelNum - 1;
        foreach ($event->specificationArray as $category) {
            $totalNum *= count($category['sub_specification']);
            $this->selectMaxBox[] = count($category['sub_specification']);
            $this->selectBox[] = 0;
        }
        $combinationList = [];
        for ($num = 0; $num < $totalNum; $num++) {
            $combination = [];
            for ($level = 0; $level < $levelNum; $level++) {
                $combination[] = $event->specificationArray[$level]['sub_specification'][$this->selectBox[$level]]['code'];
            }
            $this->moveNext($moveLevel);
            $combinationList[] = $combination;
        }
        $needCombinationList = collect($combinationList)->map(function ($combination) {
            return [
                'code' => sha1(json_encode($combination)),
                'value' => $combination
            ];
        });
        $hasCombinationList = ProductSpecification::whereProductCode($event->productModel->code)->get();
        $deleteList = $hasCombinationList->filter(function (ProductSpecification $model) use ($needCombinationList) {
            return !in_array($model->combination_code, $needCombinationList->pluck('code')->toArray());
        });
        $event->deleteSpecification = $deleteList->pluck('code');
        $createList = $needCombinationList->filter(function ($combination) use ($hasCombinationList) {
            return !in_array($combination['code'], $hasCombinationList->pluck('combination_code')->toArray());
        });
        if ($deleteList->count() !== $deleteList->filter(function (ProductSpecification $model) {
                return $model->delete();
            })->count()) {
            logs()->info('delete ProductSpecification failed.');
            return false;
        }
        if ($createList->count() !== $createList->filter(function ($combination) use ($event) {
                return (new ProductSpecification())
                    ->setAttribute('product_code', $event->productModel->code)
                    ->setAttribute('specification_codes', $combination['value'])
                    ->setAttribute('combination_code', $combination['code'])
                    ->save();
            })->count()) {
            logs()->info('create ProductSpecification failed.');
            return false;
        }
    }

    protected function moveNext($moveLevel)
    {
        if ($moveLevel < 0) {
            return;
        }
        $this->selectBox[$moveLevel] += 1;
        if ($this->selectBox[$moveLevel] >= $this->selectMaxBox[$moveLevel]) {
            $this->moveNext($moveLevel - 1);
            $this->selectBox[$moveLevel] = 0;
        }
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 5;
    }
}
