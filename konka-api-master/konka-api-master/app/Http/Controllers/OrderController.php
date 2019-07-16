<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\Setting;
use App\UserEvents\Order\CreateOrder;
use App\UserEvents\Order\PayOrder;
use App\UserEvents\Order\UpdateTrackingNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class OrderController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList(Request $request)
    {
        $searchKeywords = $request->header('X-Search-Keywords', null);
        $searchKeywords = !empty($searchKeywords) ? json_decode(base64_decode($searchKeywords), true) : [];
        $status = null;
        if (!empty($searchKeywords['status'])) {
            $status = Order::statusGroup($searchKeywords['status']);
        }
        return success(ListQueryBuilder::create(Order::with(['orderProduct'])->whereCreateUserCode(auth_user()->code))
            ->withSearch([
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_IN,
                    'value' => $status
                ],
                [
                    'key' => 'code',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => 'created_at',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN
                ]
            ])->withAppends(['status_text', 'distribution_text', 'actually_pay_price', 'is_refund', 'products'])
            ->withOrder('id', ListQueryBuilder::ORDER_TYPE_DESC)
            ->withHidden(['user_coupon_code', 'address', 'city_text', 'client_name', 'client_phone',
                'county_text', 'deleted_at', 'id', 'province_text', 'remarks', 'postal_code', 'tracking_number'])
            ->withPage()->paginateList());
    }

    /**
     * 新增
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!$result = Trigger::eventWithTransaction(new CreateOrder($request->input()))) {
            return errors('保存失败');
        }
        return success($result);
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAdminInfo($code)
    {
        if (!$order = Order::whereCode($code)->first()) {
            return errors('订单不存在');
        }
        return success($order->append([
            'status_text', 'distribution_text', 'actually_pay_price', 'products',
            'full_address'
        ]));
    }

    /**
     * 详情
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$order = Order::whereCreateUserCode(auth_user()->code)->whereCode($code)->first()) {
            return errors('订单不存在');
        }
        return success($order->append([
            'status_text', 'distribution_text', 'actually_pay_price', 'products',
            'full_address'
        ]));
    }

    /**
     * 支付订单
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function payInfo($code)
    {
        if (!$order = Order::whereCreateUserCode(auth_user()->code)->whereCode($code)->whereIn('status', [Order::STATUS_UNPAID, Order::STATUS_PAYMENT_FAILED])->first()) {
            return errors('订单不存在');
        }
        $payInfo = $order->pay_type !== Order::PAY_TYPE_WE_CHAT ? [
            'orderCode' => $order->code,
            'payType' => Order::PAY_TYPE_TRANSFER,
            'amountLimit' => Setting::getValue(Setting::SETTING_KEY_ORDER_WX_PAY_LIMIT_AMOUNT),
            'bankName' => Setting::getValue(Setting::SETTING_KEY_ORDER_PAY_BANK_NAME),
            'bankAccount' => Setting::getValue(Setting::SETTING_KEY_ORDER_PAY_BANK_ACCOUNT),
            'bankOpen' => Setting::getValue(Setting::SETTING_KEY_ORDER_PAY_BANK_OPEN),
        ] : array_merge([
            'payType' => Order::PAY_TYPE_WE_CHAT,
            'orderCode' => $order->code
        ], $order->WeChatPay());
        return success($payInfo);
    }

    /**
     * 删除订单
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function deleteInfo($code)
    {
        if (!$order = Order::whereCreateUserCode(auth_user()->code)->whereCode($code)->whereIn('status', [Order::STATUS_UNPAID, Order::STATUS_CLOSE])->first()) {
            return errors('编码无效');
        }
        if (!$order->delete()) {
            return errors('删除失败');
        }
        return success();
    }

    /**
     * 关闭订单
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function cancelInfo($code)
    {
        if (!$info = Order::whereCode($code)->whereStatus(Order::STATUS_UNPAID)->first()) {
            return errors('编码无效');
        }
        if (!$info->setAttribute('status', Order::STATUS_CLOSE)->save()) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 确认收货
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function confirmReceipt($code)
    {
        if (!$order = Order::whereCreateUserCode(auth_user()->code)->whereCode($code)->whereIn('status', [Order::STATUS_UNRECEIVED, Order::STATUS_SHIPPED])->first()) {
            return errors('订单不存在');
        }
        if (!$order->setAttribute('status', Order::STATUS_RECEIVED)->setAttribute('receive_at', Carbon::now())->save()) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 管理后台订单列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAllList(Request $request)
    {
        $searchKeywords = $request->header('X-Search-Keywords', null);
        $searchKeywords = !empty($searchKeywords) ? json_decode(base64_decode($searchKeywords), true) : [];
        $status = null;
        if (!empty($searchKeywords['status'])) {
            $status = Order::statusGroup($searchKeywords['status']);
        }
        return success(ListQueryBuilder::create(Order::query()->orderByDesc('created_at')->with(['orderInvoice', 'orderProduct', 'user']))
            ->withSearch([
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_IN,
                    'value' => $status
                ],
                [
                    'key' => 'code',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => 'created_at',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_BETWEEN
                ]
            ])->withAppends(['status_text',
                'distribution_text', 'actually_pay_price', 'pay_type_text',
                'products', 'is_invoices_text', 'product_total_number',
                'create_user_name', 'create_user_username'])
            ->withPage()->paginateList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTodayAmount()
    {
        $price = 0;
        Order::where('created_at', 'like', Carbon::now()->format('Y-m-d'))->chunk(100, function ($item) use (&$price) {
            $price += collect($item)->sum('pay_price');
        });
        return success(['amount' => $price]);
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putToPay($code, Request $request)
    {
        if (!$order = Order::whereCode($code)->whereStatus(Order::STATUS_UNPAID)->first()) {
            return errors('订单不存在');
        }
        $this->validate($request, [
            'payTime' => 'required',
            'payOrderNumber' => 'nullable'
        ], [
            'payTime.required' => '支付时间不能为空'
        ]);
        if (!Trigger::eventWithTransaction(new PayOrder($order, $request->input('payTime'), $request->input('payOrderNumber')))) {
            return errors('支付失败');
        }
        return success([], '支付成功');
    }

    /**
     * 确认发货
     *
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putConfirmDelivery($code, Request $request)
    {
        if (!$order = Order::whereCode($code)->whereStatus(Order::STATUS_NOT_SHIPPED)->first()) {
            return errors('订单不存在');
        }
        $this->validate($request, [
            'trackingNumber' => 'required',
            'trackingType' => 'required'
        ], [
            'trackingNumber.required' => '运单号不能为空',
            'trackingType.required' => '快递类型不能为空'
        ]);
        if (!$order->setAttribute('status', Order::STATUS_UNRECEIVED)
            ->setAttribute('freight_at', Carbon::now())
            ->setAttribute('tracking_number', $request->input('trackingNumber'))
            ->save()) {
            return errors('发货失败');
        }
        return success([], '发货成功');
    }

    /**
     * 修改快递单号
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putTrackingNumber(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new UpdateTrackingNumber($request->input(), $code))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 获取订单状态
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getOrderStatus($code)
    {
        if (!$order = Order::whereCode($code)->first()) {
            return errors('编码无效');
        }
        return success(['status' => $order->status]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Exception
     */

    public function export(Request $request)
    {
        /**
         * @var Order[] $orders
         */
        $orders = empty($request->header('X-Select-Codes', [])) ?
            ListQueryBuilder::create(Order::with(['orderProduct']))
                ->withSearch([
                    [
                        'key' => 'status',
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_IN,
                        'value' => function ($searchKeywords) use ($request) {
                            return !empty($searchKeywords['status']) ? Order::statusGroup($searchKeywords['status']) : null;
                        }
                    ],
                    [
                        'key' => 'code',
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => 'created_at',
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN
                    ]
                ])->withOrder('id', ListQueryBuilder::ORDER_TYPE_DESC)->get() :
            Order::whereIn('code', json_decode(base64_decode($request->header('X-Select-Codes')),true))->get();
        $data = [];
        foreach ($orders as $index => $order) {
            $data[] = [
                'serial_number' => $index + 1,
                'code' => $order->code,
                'customer_name' => $order->client_name,
                'business_name' => '康佳优品',
                'order_type' => '零售款到发货',
                'created_at' => (new Carbon($order->created_at))->format('Y-m-d H:i:s'),
                'pay_at' => (new Carbon($order->pay_at))->format('Y-m-d H:i:s'),
                'buyer' => $order->user->nickname,
                'client_name' => $order->client_name,
                'client_phone' => $order->client_phone,
                'province_text' => $order->province_text,
                'city_text' => $order->city_text,
                'county_text' => $order->county_text,
                'address' => $order->address,
                'postal_code' => $order->postal_code,
                'pay_type' => $order->pay_type === Order::PAY_TYPE_WE_CHAT ? '微信' : '线下',
                'status' => $order->status_text,
                'invoice_type' => optional($order->orderInvoice)->invoice_type_text,
                'unit_name' => optional($order->orderInvoice)->unit_name,
                'tax_ticket' => optional($order->orderInvoice)->tax_ticket,
                'open_bank' => optional($order->orderInvoice)->open_bank,
                'bank_account' => optional($order->orderInvoice)->bank_account,
                'invoice_name' => optional($order->orderInvoice)->name,
                'invoice_phone' => optional($order->orderInvoice)->phone,
                'invoice_province_text' => optional($order->orderInvoice)->province_text,
                'invoice_city_text' => optional($order->orderInvoice)->city_text,
                'invoice_county_text' => optional($order->orderInvoice)->county_text,
                'invoice_address' => optional($order->orderInvoice)->address,
                //'invoice_postal_code' => optional($order->orderInvoice)->postal_code,
                'is_install' => '否',
                'is_transport' => '是',
                'urgent' => '普通',
                'handle' => '无',
                'arrange' => '是',
                'freight' => $order->freight,
                'remarks' => $order->remarks,
                'model' => collect($order->products)->map(function ($item) {
                    return $item['title'] . '(' . $item['specifications_text'] . ')';
                })->implode(';'),
                'product_num' => $order->orderProduct->count(),
                'settlement_price' => $order->product_total_price,
                'pay_price' => $order->pay_price,
                'batch' => '无',
                'in_library' => '良好',
                'pay_number' => $order->pay_number
            ];
        }
        $head = [
            '序号', '外部订单号', '客户名称', '店铺名称', '订单类型', '订单时间', '付款时间', '买家', '收货人',
            '收货人电话', '收货人省', '收货人市', '收货人区', '收货人详细地址', '收货人邮编', '支付方式',
            '付款状态', '发票类型', '发票抬头', '纳税人识别号', '开户行', '帐号', '发票收件人', '收件人电话',
            '发票邮寄地址省', '发票邮寄地址市', '发票邮寄地址区', '发票邮寄详细地址', '是否安装', '是否运输',
            '紧急程度', '处理标识', '是否手工安排仓库', '运费', '买家留言', '机型', '商品数量', '商品结算价',
            '商品订单价', '批次', '在库状态', '子交易单号'
        ];
        $keys = [
            'serial_number', 'code', 'customer_name', 'business_name', 'order_type', 'created_at', 'pay_at', 'buyer', 'client_name',
            'client_phone', 'province_text', 'city_text', 'county_text', 'address', 'postal_code', 'pay_type',
            'status', 'invoice_type', 'unit_name', 'tax_ticket', 'open_bank', 'bank_account', 'invoice_name', 'invoice_phone',
            'invoice_province_text', 'invoice_city_text', 'invoice_county_text', 'invoice_address', 'is_install', 'is_transport',
            'urgent', 'handle', 'arrange', 'freight', 'remarks', 'model', 'product_num', 'settlement_price', 'pay_price',
            'batch', 'in_library', 'pay_number'
        ];
        return success(['fileUrl' => export_excel(Uuid::uuid4(), $data, $head, $keys)]);
    }
}
