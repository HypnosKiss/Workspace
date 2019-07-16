<?php

namespace App\UserListeners\Product\CreateProduct;


use App\Models\ProductSpecification;
use App\UserEvents\Product\CreateProduct;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteProductSpecification extends Listener
{
    protected $selectMaxBox = [];

    protected $selectBox = [];

    /**
     * @param CreateProduct $event
     * @return boolean|string|array
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
        return count($combinationList) === collect($combinationList)->filter(function ($combination) use ($event) {
                return (new ProductSpecification())
                    ->setAttribute('product_code', $event->productModel->code)
                    ->setAttribute('specification_codes', $combination)
                    ->setAttribute('combination_code', sha1(json_encode($combination)))
                    ->save();
            })->count();
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
