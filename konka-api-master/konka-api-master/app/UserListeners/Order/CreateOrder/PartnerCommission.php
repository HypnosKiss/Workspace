<?php

namespace App\UserListeners\Order\CreateOrder;

use App\Models\CommissionRule;
use App\Models\OrderProduct;
use App\Models\Partner;
use App\Models\PartnerCommissionRecord;
use App\Models\PublicDefinition;
use App\Models\Setting;
use App\UserEvents\Order\CreateOrder;
use Carbon\Carbon;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class PartnerCommission extends Listener
{
    /**
     * @var Partner
     */

    public $partner;

    /**
     * @var CreateOrder
     */

    public $event;

    /**
     * @var
     */

    protected $commissions;

    /**
     * @param CreateOrder $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        if (auth_user()->is_partner !== PublicDefinition::SWITCH_YES && empty($event->input['partnerCode'])) {
            return true;
        }
        if (auth_user()->is_partner === PublicDefinition::SWITCH_YES) {
            $this->partner = auth_user()->partner;
        } elseif (!empty($event->input['partnerCode'])) {
            $this->partner = Partner::whereInviteCode($event->input['partnerCode'])->first();
        }
        if (empty($this->partner)) {
            logs()->info('partner document not found.');
            return true;
        }
        $this->event = $event;
        if (!$this->writeFirstCommission()) {
            throw new CustomException('写入一级佣金记录失败');
        }
        if (!$this->writeSecondCommission()) {
            throw new CustomException('写入二级佣金记录失败');
        }
    }

    /**
     * @return integer
     */

    public function order()
    {
        return 5;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    protected function calculateCommission()
    {
        if ($this->commissions === null) {
            $this->commissions = collect($this->event->orderProduct)->map(function (OrderProduct $orderProduct) {
                $return = array_merge([
                    'productCode' => $orderProduct->product_code,
                    'productSpecificationsCode' => $orderProduct->product_specifications_code,
                    'productPrice' => $orderProduct->price,
                    'productNum' => $orderProduct->num,
                    'totalAmount' => round($orderProduct->price * $orderProduct->num, 2),
                ], CommissionRule::getCommissionProportion($this->partner->code, $orderProduct->product_code));
                $return['firstCommissionAmount'] = round($return['totalAmount'] * ($return['firstPercentage'] / 100), 2);
                $return['secondCommissionAmount'] = round($return['totalAmount'] * ($return['secondPercentage'] / 100), 2);
                return $return;
            });
        }
        logs()->info('commission composition', $this->commissions->toArray());
        return $this->commissions;
    }

    /**
     * @return mixed
     */

    public function writeFirstCommission()
    {
        $commissions = $this->calculateCommission();
        return (new PartnerCommissionRecord())
            ->setAttribute('order_code', $this->event->orderModel->code)
            ->setAttribute('type', PartnerCommissionRecord::TYPE_FIRST_LEVEL)
            ->setAttribute('order_amount', $this->event->orderModel->product_total_price)
            ->setAttribute('order_pay_amount', $this->event->orderModel->actually_pay_price)
            ->setAttribute('convert_ratio', Setting::getValue(Setting::SETTING_KEY_PARTNER_AMOUNT_TO_INTEGRAL_RATIO))
            ->setAttribute('should_unlock_time', Carbon::now())
            ->setAttribute('partner_code', $this->partner->code)
            ->setAttribute('commission_composition', $commissions)
            ->setAttribute('total_commission_amount', collect($commissions)->sum('firstCommissionAmount'))
            ->save();
    }

    /**
     * @return bool
     */

    public function writeSecondCommission()
    {
        if (empty($this->partner->parent_code)) {
            return true;
        }
        if ($this->partner->code !== auth_user()->partner_code) {
            logs()->info('此订单是邀请码订单不计算二级佣金');
            return true;
        }
        $commissions = $this->calculateCommission();
        return (new PartnerCommissionRecord())
            ->setAttribute('order_code', $this->event->orderModel->code)
            ->setAttribute('type', PartnerCommissionRecord::TYPE_FIRST_LEVEL)
            ->setAttribute('order_amount', $this->event->orderModel->product_total_price)
            ->setAttribute('order_pay_amount', $this->event->orderModel->actually_pay_price)
            ->setAttribute('convert_ratio', Setting::getValue(Setting::SETTING_KEY_PARTNER_AMOUNT_TO_INTEGRAL_RATIO))
            ->setAttribute('should_unlock_time', Carbon::now())
            ->setAttribute('partner_code', $this->partner->parent_code)
            ->setAttribute('commission_composition', $commissions)
            ->setAttribute('total_commission_amount', collect($commissions)->sum('secondCommissionAmount'))
            ->save();
    }
}
