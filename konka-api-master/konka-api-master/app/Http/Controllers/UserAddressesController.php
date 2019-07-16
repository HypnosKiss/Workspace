<?php

namespace App\Http\Controllers;


use App\Models\UserAddresses;
use App\UserEvents\Addresses\CreateAddresses;
use App\UserEvents\Addresses\UpdateAddresses;
use App\UserEvents\Addresses\UpdateStatus;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\Trigger;

class UserAddressesController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(UserAddresses::whereUserCode(auth_user()->code))->getList());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateAddresses($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new UpdateAddresses($request->input(), $code))) {
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
        if (!$info = UserAddresses::whereUserCode(auth_user()->code)->whereCode($code)->first()) {
            return errors('编码无效');
        }
        return success($info);
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function deleteInfo($code)
    {
        if (!$info = UserAddresses::whereUserCode(auth_user()->code)->whereCode($code)->first()) {
            return errors('编码无效');
        }
        if (!$info->delete()) {
            return errors('删除失败');
        }
        return success();
    }

    /**
     * 修改默认
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putInfoStatus($code)
    {
        if (!Trigger::eventWithTransaction(new UpdateStatus($code))) {
            return errors('保存失败');
        }
        return success();
    }
}