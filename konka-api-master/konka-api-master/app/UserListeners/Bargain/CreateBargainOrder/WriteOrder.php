<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;


use App\Models\BaseInfo;
use App\Models\CouponUser;
use App\Models\Order;
use App\Models\UserAddresses;
use App\Models\UserBargainProduct;
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
        $this->checkStatus($event);
        $productPrice = $this->productPrice($event);
        $couponPrice = $this->couponPrice($event, $productPrice);
        Log::info('Order Price Calculate', [
            'productPrice' => $productPrice,
            'couponPrice' => $couponPrice,
            'freight' => $event->input['freight']
        ]);
        if ((float)$event->input['actuallyPayPrice'] !== ($productPrice + (int)$event->input['freight'] - $couponPrice)) {
            throw new CustomException('实付金额错误');
        }
        if (BaseInfo::getSetting(BaseInfo::TYPE_AMOUNT_LIMIT) < $event->input['actuallyPayPrice']) {
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
            ->setAttribute('user_coupon_code', $event->input['userCouponCode'])
            ->setAttribute('product_total_price', $productPrice)
            ->setAttribute('discounted_price', $couponPrice)
            ->setAttribute('freight', $event->input['freight'])->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }

    protected function checkStatus($event){
        if($event->userBargainProduct->status !== UserBargainProduct::STATUS_CAN_BUY){
            throw new CustomException('砍价未完成');
        }
        if($event->bargainProduct->product_specifications_code !== $event->input['products'][0]['productSpecificationCodes']){
            throw new CustomException('砍价商品异常');
        }
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
            return $event->bargainProduct->after_price * $item['num'];
        })->sum();
    }

    /**
     * 优惠金额
     *
     * @param CreateOrder $event
     * @param $productPrice
     * @return int
     */
    protected function couponPrice($event, $productPrice)
    {
        if (empty($event->input['userCouponCode'])) {
            return 0;
        }
        if (!$userCoupon = CouponUser::whereCode($event->input['userCouponCode'])->first()) {
            throw new CustomException('优惠劵不存在');
        }
        if ($userCoupon->conditions === CouponUser::CONDITIONS_FULL_REDUCTION) {
            if ($userCoupon->conditions_value > $productPrice) {
                throw new CustomException('优惠劵错误');
            }
        }
        return $userCoupon->type === CouponUser::TYPE_PREFERENTIAL ? (int)$userCoupon->discount : round($productPrice / $userCoupon->discount, 2);
    }
}
