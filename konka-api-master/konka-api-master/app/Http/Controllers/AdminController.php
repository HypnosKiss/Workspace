<?php

namespace App\Http\Controllers;


use App\Models\Admin;
use App\UserEvents\Administrator\BindRole;
use App\UserEvents\Administrator\CreateAdministrator;
use App\UserEvents\Administrator\Login;
use App\UserEvents\Administrator\UpdateAdministrator;
use App\UserEvents\Administrator\UpdateAdministratorStatusDisable;
use App\UserEvents\Administrator\UpdateAdministratorStatusEnable;
use App\UserEvents\Administrator\UpdateCurrentPassword;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Exceptions\CustomException;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class AdminController extends Controller
{
    /**
     * 后台登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function login(Request $request)
    {
        if (!$result = Trigger::eventResult(new Login($request->input()))) {
            return errors('登录失败');
        }
        return success($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout(Request $request)
    {
        $request->user()->logout();
        return success();
    }

    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Admin::query())
            ->withSearch([
                [
                    'key' => 'username',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE,
                ], [
                    'key' => 'code',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ]
            ])->withPage()->paginateList()
        );
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
        if (!Trigger::eventWithTransaction(new CreateAdministrator($request->input()))) {
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
        if (!Trigger::eventWithTransaction(new UpdateAdministrator($request->input(), $code))) {
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
        if (!$info = Admin::whereCode($code)->first()) {
            return errors('用户编码不存在');
        }
        return success($info->append(['permissions', 'roles']));
    }

    /**
     * 删除
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteInfo($code)
    {
        if (!$info = Admin::whereCode($code)->first()) {
            return errors('用户编码不存在');
        }
        if (!$info->toDelete()) {
            return errors('删除失败');
        }
        return success([], '删除成功');
    }

    /**
     * 启用
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusEnable($code)
    {
        if (!$info = Admin::whereCode($code)->first()) {
            return errors('用户编码不存在');
        }
        if (!$info->toEnabled()) {
            return errors('启用失败');
        }
        return success([], '启用成功');
    }

    /**
     * 禁用
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusDisable($code)
    {
        if (!$info = Admin::whereCode($code)->first()) {
            return errors('用户编码不存在');
        }
        if (!$info->toDisabled()) {
            return errors('禁用失败');
        }
        return success([], '禁用成功');
    }

    /**
     * 重置密码
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putResetPassword($code)
    {
        if (!$info = Admin::whereCode($code)->first()) {
            return errors('用户编码不存在');
        }
        if ($info->code === auth_admin()->code) {
            return errors('不能重置本账号密码');
        }
        $password = str_random(8);
        if (!$info->changePassword($password)) {
            return errors('重置密码失败');
        }
        return success(['password' => $password], '重置密码成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getCurrentInfo()
    {
        return success(auth_admin());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putCurrentPassword(Request $request)
    {
        $this->validate($request, [
            'oldPassword' => ['required'],
            'newPassword' => ['required'],
            'confirmPassword' => ['required', 'same:newPassword']
        ], [
            'oldPassword.required' => '旧密码不能为空',
            'newPassword.required' => '新密码不能为空',
            'confirmPassword.required' => '确认密码不能为空',
            'confirmPassword.same' => '确认密码与新密码不一致',
        ]);
        if (!auth_admin()->validatePassword($request->input('oldPassword'))) {
            throw new CustomException('旧密码不正确');
        }
        if (!auth_admin()->changePassword($request->input('newPassword'))) {
            return errors('修改密码失败');
        }
        return success([], '修改密码成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTypeList()
    {
        return success(definition_to_select(Admin::getTypeLabels()));
    }
}
