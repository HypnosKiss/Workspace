<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\UserEvents\Order\PayOrder;
use Exception;
use Log;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\Trigger;

class NotifyController extends Controller
{
    /**
     * @return mixed
     */

    public function orderPay()
    {
        return app('wechat.payment')->handlePaidNotify(function ($message, $fail) {
            Log::info('weChat notify', $message);
            try {
                if (!$order = Order::whereStatus(Order::STATUS_UNPAID)->whereCode($message['out_trade_no'])->first()) {
                    Log::info('order not exists', collect($order)->toArray());
                    return true;
                }
                Log::info('order exists', collect($order)->toArray());
                if ($message['return_code'] === 'SUCCESS') {
                    if (array_get($message, 'result_code') === 'SUCCESS') {
                        if ($order->actually_pay_price != round($message['total_fee'] / 100, 2)) {
                            $order->setAttribute('status', Order::STATUS_ABNORMAL)->save();
                            return true;
                        }
                        Trigger::eventWithTransaction(new PayOrder($order, null, $message['transaction_id']));
                    } elseif (array_get($message, 'result_code') === 'FAIL') {
                        $order->setAttribute('status', Order::STATUS_PAYMENT_FAILED)->save();
                    }
                } else {
                    return $fail('通信失败，请稍后再通知我');
                }
                $order->save();
                return true;
            } catch (Exception $exception) {
                Log::error('WeChat notify Exception', ['message' => $exception->__toString()]);
                return true;
            }
        });
    }
}
