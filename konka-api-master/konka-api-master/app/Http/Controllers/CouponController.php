<?php

namespace App\Http\Controllers;


use App\Models\Coupon;
use App\UserEvents\Coupon\AppletsReceiveCoupon;
use App\UserEvents\Coupon\CreateCoupon;
use App\UserEvents\Coupon\DeleteCoupon;
use App\UserEvents\Coupon\UpdateCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class CouponController extends Controller
{
    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Coupon::query())->withSearch([
            [
                'key' => 'code',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
            ], [
                'key' => 'name',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
            ], [
                'key' => 'type',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
            ]
        ])->withPage()->paginateList());
    }

    /**
     * 获取可用不通用优惠劵列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAvailableList()
    {
        return success(ListQueryBuilder::create(Coupon::whereStatus(Coupon::STATUS_NO_GLOBAL)->where('end_at', '<', Carbon::now()))->getList());
    }

    /**
     * 获取可用通用优惠劵列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getGeneralPurposeList()
    {
        return success(ListQueryBuilder::create(Coupon::whereStatus(Coupon::STATUS_GLOBAL)->where('end_at', '<', Carbon::now()))->getList());
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
        if (!Trigger::eventWithTransaction(new CreateCoupon($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 修改
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new UpdateCoupon($request->input(), $code))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 详情
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = Coupon::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        return success($info);
    }

    /**
     * 删除
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteInfo($code)
    {
        if (!Trigger::eventWithTransaction(new DeleteCoupon($code))) {
            return errors('删除失败');
        }
        return success();
    }

    /**
     * 用户领取优惠劵
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postAppletsReceiveCoupon(Request $request)
    {
        if (!Trigger::eventWithTransaction(new AppletsReceiveCoupon($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }
}