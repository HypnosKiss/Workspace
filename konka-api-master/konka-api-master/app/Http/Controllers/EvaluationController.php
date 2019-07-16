<?php

namespace App\Http\Controllers;


use App\Models\Evaluation;
use App\UserEvents\Evaluation\CreateEvaluation;
use App\UserEvents\Evaluation\CreateEvaluationReply;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\Trigger;

class EvaluationController extends Controller
{
    /**
     * @param $productCode
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList($productCode)
    {
        return success(ListQueryBuilder::create(Evaluation::whereProductCode($productCode))->withPage()->paginateList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAdminList()
    {
        return success(ListQueryBuilder::create(Evaluation::query())->withPage()->paginateList());
    }

    /**
     * 评价
     *
     * @param Request $request
     * @param $orderCode
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request, $orderCode)
    {
        if (!Trigger::eventWithTransaction(new CreateEvaluation($request->input(), $orderCode))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 评价回复
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postReply(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new CreateEvaluationReply($request->input(), $code))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 评价详情
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = Evaluation::with(['evaluationReply'])->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        return success($info);
    }
}
