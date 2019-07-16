<?php

namespace App\UserListeners\Bargain\CreateBargainOrder;


use App\Models\Balance;
use App\Models\CapitalFlow;
use App\Models\CouponUser;
use App\Models\Partner;
use App\Models\PartnerCommissionSetting;
use App\UserEvents\Order\CreateOrder;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class PartnerCommission extends Listener
{

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (!auth_user()->is_partner) {
            return true;
        }
        $personalCommission = $this->getProductTotalPriceCommission($event, auth_user()->partner->points);
        $productCommission = 0;
        foreach ($event->productSpecifications->sortByDesc('price')->values() as $index => $productSpecification) {
            $price = $productSpecification->price;
            if ($index === 0 && !empty($event->input['userCouponCode'])) {
                $price = $productSpecification->price - $this->couponPrice($event->input['userCouponCode'], $productSpecification->price);
                if ($price < 0) {
                    throw new CustomException('优惠价格出现负数不能下单,请联系管理员');
                }
            }
            $orderProduct = collect($event->orderProduct)->firstWhere('product_specifications_code', $productSpecification->code);
            $productCommission += round($price * $orderProduct['num'] * ($productSpecification->distribution_num / 100), 2);
        }
        $price = $personalCommission > $productCommission ? $personalCommission : $productCommission;
        $this->writeData($price, auth_user()->code, $event->orderModel->code);
        if (auth_user()->partner->is_top) {
            return true;
        }
        if (!$setting = PartnerCommissionSetting::wherePartnerCode(auth_user()->partner->code)->first()) {
            return true;
        }
        //上级合伙人
        $parentPartner = Partner::whereCode(auth_user()->partner->parent_code)->first();
        $parentPartnerSettingCommission = $setting->type === PartnerCommissionSetting::TYPE_FIXED ?
            $setting->one_level_commission : $this->getProductTotalPriceCommission($event, $setting->one_level_commission);
        $this->writeData($parentPartnerSettingCommission, $parentPartner->user_code, $event->orderModel->code);
        if (empty($parentPartner->parent_code)) {
            return true;
        }
        //上上级合伙人
        $topPartner = Partner::whereCode($parentPartner->parent_code)->first();
        $topPartnerSettingCommission = $setting->type === PartnerCommissionSetting::TYPE_FIXED ?
            $setting->two_level_commission : $this->getProductTotalPriceCommission($event, $setting->two_level_commission);
        $this->writeData($topPartnerSettingCommission, $topPartner->user_code, $event->orderModel->code);
        return true;
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 6;
    }

    protected function couponPrice($userCouponCode, $productPrice)
    {
        if (!$userCoupon = CouponUser::whereCode($userCouponCode)->first()) {
            throw new CustomException('优惠劵不存在');
        }
        if ($userCoupon->conditions === CouponUser::CONDITIONS_FULL_REDUCTION) {
            if ($userCoupon->conditions_value > $productPrice) {
                throw new CustomException('优惠劵错误');
            }
        }
        return $userCoupon->type === CouponUser::TYPE_PREFERENTIAL ? (int)$userCoupon->discount : round($productPrice / $userCoupon->discount, 2);
    }

    /**
     * @param CreateOrder $event
     * @param $points
     * @return float
     */
    protected function getProductTotalPriceCommission($event, $points)
    {
        return round(collect($event->orderProduct)->map(function ($item) {
                return $item['price'] * $item['num'];
            })->sum() * $points / 100, 2);
    }

    /**
     * @param $price
     * @param $userCode
     * @param $orderCode
     * @param null $remarks
     * @return bool
     */

    protected function writeData($price, $userCode, $orderCode, $remarks = null)
    {
        if (!$this->writeBalance($price, $userCode)) {
            throw new CustomException('写入余额失败');
        }
        if (!$this->writeCapitalFlow($price, $orderCode, $userCode, $remarks)) {
            throw new CustomException('写入流水失败');
        }
        return true;
    }

    protected $totalMoney;

    /**
     * @param $price
     * @param $userCode
     * @return mixed
     */

    protected function writeBalance($price, $userCode)
    {
        if (!$balance = Balance::whereUserCode($userCode)->first()) {
            $balance = (new Balance())->setAttribute('user_code', $userCode);
        }
        $this->totalMoney = $balance->total_money + $price;
        return $balance->setAttribute('total_money', $this->totalMoney)
            ->setAttribute('frozen_money', $balance->frozen_money + $price)
            ->save();
    }

    /**
     * @param $price
     * @param $orderCode
     * @param $userCode
     * @param $remarks
     * @return mixed
     */

    protected function writeCapitalFlow($price, $orderCode, $userCode, $remarks = '佣金')
    {
        return (new CapitalFlow())->setAttribute('user_code', $userCode)
            ->setAttribute('money_change', $price)
            ->setAttribute('final_money', $this->totalMoney)
            ->setAttribute('type', CapitalFlow::TYPE_INCOME)
            ->setAttribute('order_code', $orderCode)
            ->setAttribute('document_type', CapitalFlow::DOCUMENT_TYPE_DISTRIBUTION)
            ->setAttribute('remarks', $remarks)
            ->save();
    }
}
