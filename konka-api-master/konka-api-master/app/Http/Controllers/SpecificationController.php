<?php

namespace App\Http\Controllers;


use App\Models\Specification;
use App\UserEvents\Specification\CreateSpecification;
use App\UserEvents\Specification\DeleteSpecification;
use App\UserEvents\Specification\UpdateSpecification;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\Trigger;

class SpecificationController extends Controller
{
    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Specification::query())
            ->withPage()
            ->paginateList());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateSpecification($request->input()))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = Specification::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        return success($info);
    }

    /**
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new UpdateSpecification($request->input(), $code))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteInfo($code)
    {
        if (!Trigger::eventWithTransaction(new DeleteSpecification($code))) {
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

    public function putEnable($code)
    {
        if (!$info = Specification::disable()->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->setAttribute('status', Specification::STATUS_ENABLE)->save()) {
            return errors('保存失败');
        }
        return success([], '启用成功');
    }

    /**
     * 禁用
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putDisable($code)
    {
        if (!$info = Specification::enable()->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->setAttribute('status', Specification::STATUS_DISABLE)->save()) {
            return errors('保存失败');
        }
        return success([], '禁用成功');
    }

    /**
     * 获取所有顶级规格
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTopEnable()
    {
        return success(ListQueryBuilder::create(Specification::whereLevel(1)->whereStatus(Specification::STATUS_ENABLE))->getList());
    }


    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSubList($code)
    {
        if (!$info = Specification::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        return success($info->subSpecification->where('status', Specification::STATUS_ENABLE)->values());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAllEnableList()
    {
        return success(ListQueryBuilder::create(Specification::enable()->whereLevel(1)->with(['subSpecification']))
            ->withOrder('order', 'asc')
            ->withHidden(['subSpecification'])
            ->withAppends(['sub_enable_specification'])
            ->getList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSpecificationCategoryList()
    {
        return success(Specification::whereNull('parent_code')->get());
    }

    /**
     *
     */

    public function getCombinationList()
    {
        $combinationList = collect([]);
        Specification::whereNull('parent_code')->get()->each(function (Specification $specification) use ($combinationList) {
            $specification->subSpecification->each(function ($sub) use ($specification, $combinationList) {
                $combinationList->push(['label' => $specification['name'] . '/' . $sub['name'], 'key' => $sub['code'], 'disabled' => false]);
            });
        });
        return success($combinationList);
    }
}
