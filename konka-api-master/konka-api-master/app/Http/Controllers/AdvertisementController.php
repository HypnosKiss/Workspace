<?php

namespace App\Http\Controllers;


use App\Models\Advertisement;
use App\UserEvents\Advertisement\CreateAdvertisement;
use App\UserEvents\Advertisement\UpdateAdvertisement;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\Trigger;

class AdvertisementController extends Controller
{

    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Advertisement::query())->withPage()->withAppends([
                'create_person_name',
                'status_text',
                'content_url',
                'position_name'
            ]
        )->paginateList());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateAdvertisement($request->input()))) {
            return errors('广告创建失败');
        }
        return success([], '广告创建成功');
    }

    /**
     * 详情
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = Advertisement::whereCode($code)->first()) {
            return errors('不存在的广告');
        }
        return success($info->append([
            'create_person_name',
            'status_text',
            'position_name',
            'content_url']));
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
        if (!Trigger::eventWithTransaction(new UpdateAdvertisement($request->input(), $code))) {
            return errors('广告修改失败');
        }
        return success([], '广告修改成功');
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
        if (!$info = Advertisement::whereCode($code)->first()) {
            return errors('不存在的广告');
        }
        if (!$info->delete()) {
            return errors('删除失败');
        }
        return success([], '删除成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusEnable($code)
    {
        if (!$info = Advertisement::whereCode($code)->first()) {
            return errors('编码无效');
        }
        if (!$info->setAttribute('status', Advertisement::STATUS_ENABLE)->save()) {
            return errors('保存失败');
        }
        return success([], '启用成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusDisable($code)
    {
        if (!$info = Advertisement::whereCode($code)->first()) {
            return errors('编码无效');
        }
        if (!$info->setAttribute('status', Advertisement::STATUS_DISABLE)->save()) {
            return errors('保存失败');
        }
        return success([], '禁用成功');
    }

    /**
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putOrder(Request $request, $code)
    {
        if (!$info = Advertisement::whereCode($code)->first()) {
            return errors('编码无效');
        }
        if (!$info->setAttribute('order', $request->input('order', 0))->save()) {
            return errors('保存失败');
        }
        return success([], '');
    }

    /**
     * 获取上架5条广告
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getEnableList()
    {
        return success(ListQueryBuilder::create(Advertisement::enable()->orderBy('order', 'asc')->limit(5))->getList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getPositionList()
    {
        return success(definition_to_select(Advertisement::getPositionLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getContactTypeList()
    {
        return success(definition_to_select(Advertisement::getContactTypeLabels()));
    }
}
