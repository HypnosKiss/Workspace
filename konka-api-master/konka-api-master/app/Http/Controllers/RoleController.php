<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\UserEvents\Role\CreateRole;
use App\UserEvents\Role\UpdateRole;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class RoleController extends Controller
{
    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Role::query())
            ->withSearch([
                [
                    'key' => 'name',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
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
        if (!Trigger::eventWithTransaction(new CreateRole($request->input()))) {
            return errors('保存角色失败');
        }
        return success([], '保存角色成功');
    }

    /**
     * 详情
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = Role::whereCode($code)->first()) {
            return errors('角色不存在');
        }
        return success($info->append(['permissions']));
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
        if (!$info = Role::whereCode($code)->first()) {
            return errors('角色不存在');
        }
        if (!Trigger::eventWithTransaction(new UpdateRole($request->input(), $info))) {
            return errors('保存角色失败');
        }
        return success([], '保存角色成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteInfo($code)
    {
        if (!$info = Role::whereCode($code)->first()) {
            return errors('角色不存在');
        }
        if (!$info->toDelete()) {
            return errors('删除角色失败');
        }
        return success([], '删除角色成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusEnable($code)
    {
        if (!$info = Role::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->toEnabled()) {
            return errors('启用失败');
        }
        return success([], '启用成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusDisable($code)
    {
        if (!$info = Role::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->toDisabled()) {
            return errors('禁用失败');
        }
        return success([], '禁用成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getEnableList()
    {
        return success(Role::enabled()->get());
    }
}
