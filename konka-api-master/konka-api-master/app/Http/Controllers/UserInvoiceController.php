<?php

namespace App\Http\Controllers;

use App\Models\UserInvoice;
use App\UserEvents\Invoice\CreateInvoice;
use App\UserEvents\Invoice\UpdateInvoice;
use App\UserEvents\Invoice\UpdateStatus;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\Trigger;

class UserInvoiceController extends Controller
{
    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(UserInvoice::whereUserCode(auth_user()->code)->get());
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
        if (!Trigger::eventWithTransaction(new CreateInvoice($request->input()))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
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
        if (!Trigger::eventWithTransaction(new UpdateInvoice($request->input(), $code))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * 详情
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = UserInvoice::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        return success($info->toArray());
    }

    /**
     * 删除
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function deleteInfo($code)
    {
        if (!$info = UserInvoice::whereUserCode(auth_user()->code)->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->toDelete()) {
            return errors('删除失败');
        }
        return success([], '删除成功');
    }

    /**
     * 修改默认状态
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putInfoStatus($code)
    {
        if (!Trigger::eventWithTransaction(new UpdateStatus($code))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }
}
