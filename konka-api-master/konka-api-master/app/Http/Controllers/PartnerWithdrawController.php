<?php

namespace App\Http\Controllers;


use App\Models\PartnerWithdraw;
use App\UserEvents\PartnerWithdraw\BusinessApprovalFailed;
use App\UserEvents\PartnerWithdraw\BusinessApproved;
use App\UserEvents\PartnerWithdraw\CreateWithdraw;
use App\UserEvents\PartnerWithdraw\FinanceApprovalFailed;
use App\UserEvents\PartnerWithdraw\FinanceApproved;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class PartnerWithdrawController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList(Request $request)
    {
        $searchKeywords = !empty($request->header('X-Search-Keywords', null)) ?
            json_decode(base64_decode($request->header('X-Search-Keywords', null)), true) : [];
        $status = null;
        if (!empty($searchKeywords['status'])) {
            if ((int)$searchKeywords['status'] === PartnerWithdraw::STATUS_BUSINESS_APPROVAL) {
                $status = [PartnerWithdraw::STATUS_BUSINESS_APPROVAL, PartnerWithdraw::STATUS_FINANCIAL_APPROVAL, PartnerWithdraw::STATUS_FINANCIAL_APPROVAL_FAILURE];
            } else {
                $status = [$searchKeywords['status']];
            }
        }
        return success(ListQueryBuilder::create(PartnerWithdraw::wherePartnerCode(auth_user()->partner->code))
            ->withSearch([
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_IN,
                    'value' => $status
                ], [
                    'key' => 'created_at',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN
                ]
            ])
            ->withPage()
            ->paginateList());
    }

    /**
     * 新增提现
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateWithdraw($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 获取所有提现记录
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllList()
    {
        return success(ListQueryBuilder::create(PartnerWithdraw::with(['approvalRecord']))
            ->withSearch([[
                'key' => 'created_at',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN
            ], [
                'key' => 'status',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
            ]
            ])->withPage()->paginateList());
    }

    /**
     * 业务审核列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getBusinessList()
    {
        return success(ListQueryBuilder::create(PartnerWithdraw::with(['approvalRecord']))
            ->withSearch([[
                'key' => 'created_at',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN
            ], [
                'key' => 'status',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_IN,
                'value' => [PartnerWithdraw::STATUS_BUSINESS_APPROVAL, PartnerWithdraw::STATUS_FINANCIAL_APPROVAL_FAILURE]
            ]
            ])->withPage()->paginateList());
    }

    /**
     * 业务审批通过
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putBusinessApproved($code)
    {
        if (!Trigger::eventWithTransaction(new BusinessApproved($code))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 业务审批不通过
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putBusinessApprovalFailed(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new BusinessApprovalFailed($request->input('reason'), $code))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 财务审核列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFinanceList()
    {
        return success(ListQueryBuilder::create(PartnerWithdraw::with(['approvalRecord']))
            ->withSearch([[
                'key' => 'created_at',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN
            ], [
                'key' => 'status',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH,
                'value' => PartnerWithdraw::STATUS_FINANCIAL_APPROVAL
            ]
            ])->withPage()->paginateList());
    }

    /**
     * 财务审核成功
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putFinanceApproved($code)
    {
        if (!Trigger::eventWithTransaction(new FinanceApproved($code))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 财务审核失败
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putFinanceApprovalFailed(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new FinanceApprovalFailed($request->input(), $code))) {
            return errors('保存失败');
        }
        return success();
    }
}