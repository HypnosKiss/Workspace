<?php

namespace App\Http\Controllers;


use App\Models\RefundOrder;
use App\UserEvents\RefundOrder\CreateRefundOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class RefundOrderController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(RefundOrder::whereUserCode(auth_user()->code)->orderByDesc('created_at'))->withPage()->paginateList());
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
        if (!Trigger::eventWithTransaction(new CreateRefundOrder($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = RefundOrder::whereCode($code)->first()) {
            return errors('编码无效');
        }
        return success($info);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAllList()
    {
        return success(ListQueryBuilder::create(RefundOrder::query())->withSearch([
            [
                'key' => 'code',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE,
            ],
            [
                'key' => 'type',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH,
            ],
            [
                'key' => 'refund_type',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH,
            ],
            [
                'key' => 'created_at',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN,
            ],
            [
                'key' => 'status',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH,
            ]
        ])->withPage()
            ->withAppends(['type_text', 'refund_type_text', 'status_text'])
            ->paginateList());
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putAudit($code, Request $request)
    {
        if (!$model = RefundOrder::whereCode($code)->first()) {
            return errors('单据不存在');
        }
        $this->validate($request, [
            'type' => ['required', Rule::in(RefundOrder::getTypeList())]
        ], [
            'type.required' => '处理类型不能为空',
            'type.in' => '处理类型不正确'
        ]);
        $status = (int)$request->input('type') === 10 ? RefundOrder::STATUS_PASS_REFUND : RefundOrder::STATUS_PASS_EXCHANGE;
        if (!$model->setAttribute('status', $status)->save()) {
            return errors('审核失败');
        }
        return success([], '审核成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putRefund($code)
    {
        if (!$model = RefundOrder::whereCode($code)->first()) {
            return errors('单据不存在');
        }
        if (!$model->setAttribute('status', RefundOrder::STATUS_FINISH)->save()) {
            return errors('退款失败');
        }
        return success([], '退款成功');
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putDelivery($code, Request $request)
    {
        if (!$model = RefundOrder::whereCode($code)->first()) {
            return errors('单据不存在');
        }
        $this->validate($request, [
            'trackingNumber' => ['required'],
        ], [
            'trackingNumber.required' => '运单号不能为空'
        ]);
        if (!$model->setAttribute('tracking_number', $request->input('trackingNumber'))
            ->setAttribute('status', RefundOrder::STATUS_EXCHANGE_SEND)
            ->save()) {
            return errors('发货失败');
        }
        return success([], '发货成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function confirmInfo($code)
    {
        if (!$model = RefundOrder::whereCode($code)->first()) {
            return errors('单据不存在');
        }
        if (!$model->setAttribute('status', RefundOrder::STATUS_FINISH)->save()) {
            return errors('确认收货失败');
        }
        return success([], '确认收货');
    }
}
