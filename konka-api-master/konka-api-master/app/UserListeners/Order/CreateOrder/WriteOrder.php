<?php

namespace App\UserListeners\Order\CreateOrder;


use App\Models\BaseInfo;
use App\Models\CouponUser;
use App\Models\Order;
use App\Models\Setting;
use App\Models\UserAddresses;
use App\UserEvents\Order\CreateOrder;
use Log;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteOrder extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $productPrice = $this->productPrice($event);
        if ((float)$event->input['actuallyPayPrice'] !== round($productPrice + (int)$event->input['freight'], 2)) {
            throw new CustomException('实付金额错误');
        }
        if (Setting::getValue(Setting::SETTING_KEY_ORDER_WX_PAY_LIMIT_AMOUNT) < $event->input['actuallyPayPrice']) {
            $event->input['payType'] = Order::PAY_TYPE_TRANSFER;
        }
        $addresses = UserAddresses::whereCode($event->input['addressesCode'])->first();
        $fields = [
            'province_code', 'province_text', 'city_code', 'city_text', 'county_code', 'county_text',
            'address', 'postal_code'
        ];
        $event->orderModel = new Order();
        return $event->orderModel->fillable($fields)->fill($addresses->only($fields))
            ->setAttribute('client_name', $addresses->name)
            ->setAttribute('client_phone', $addresses->phone)
            ->setAttribute('type', Order::TYPE_ORDINARY)
            ->setAttribute('remarks', optional($event->input)['remarks'])
            ->setAttribute('distribution', $event->input['distribution'])
            ->setAttribute('product_total_price', $productPrice)
            ->setAttribute('freight', $event->input['freight'])
            ->setAttribute('pay_type', $event->input['payType'])
            ->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }

    /**
     * 商品总金额
     *
     * @param CreateOrder $event
     * @return mixed
     */

    protected function productPrice($event)
    {
        return collect($event->input['products'])->map(function ($item) use ($event) {
            $specification = $event->productSpecifications->firstWhere('code', $item['productSpecificationCodes']);
            return round($specification['price'] * $item['num'], 2);
        })->sum();
    }
}
