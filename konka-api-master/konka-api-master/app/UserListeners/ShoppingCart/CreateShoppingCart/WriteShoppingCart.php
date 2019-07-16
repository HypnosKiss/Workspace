<?php

namespace App\UserListeners\ShoppingCart\CreateShoppingCart;


use App\Models\ProductSpecification;
use App\Models\ShoppingCart;
use App\UserEvents\ShoppingCart\CreateShoppingCart;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteShoppingCart extends Listener
{

    /**
     * @param CreateShoppingCart $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!$productSpecification = ProductSpecification::whereCode($event->input['productSpecificationCode'])->first()) {
            throw new CustomException('产品规格编码不存在');
        }
        if ($shoppingCart = ShoppingCart::whereProductSpecificationCode($event->input['productSpecificationCode'])->whereUserCode(auth_user()->code)->first()) {
            return $shoppingCart->setAttribute('num', $shoppingCart->num + $event->input['num'])->save();
        }
        return (new ShoppingCart())->setAttribute('product_specification_code', $event->input['productSpecificationCode'])
            ->setAttribute('num', $event->input['num'])
            ->setAttribute('user_code', auth_user()->code)
            ->setAttribute('product_code', $productSpecification->product_code)->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
