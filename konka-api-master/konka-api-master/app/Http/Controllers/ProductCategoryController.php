<?php

namespace App\Http\Controllers;


use App\Models\ProductCategory;
use App\UserEvents\ProductCategory\CreateProductCategory;
use App\UserEvents\ProductCategory\DeleteProductCategory;
use App\UserEvents\ProductCategory\UpdateProductCategory;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\Trigger;

class ProductCategoryController extends Controller
{
    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(ProductCategory::query())
            ->withPage()
            ->paginateList());
    }

    /**
     * 启用列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getEnableList()
    {
        $list = ListQueryBuilder::create(ProductCategory::enable()->whereNull('parent_code')->with(['subCategory']))
            ->withOrder('order', 'asc')
            ->getList();
        return success(collect($list)->map(function ($item) {
            $info = $item;
            $info['sub_category'] = collect($item['sub_category'])->where('status', ProductCategory::STATUS_ENABLE);
            return $info;
        }));
    }

    /**
     * 新增分类
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateProductCategory($request->input()))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * 修改分类
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new UpdateProductCategory($request->input(), $code))) {
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
        if (!$info = ProductCategory::whereCode($code)->first()) {
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
        if (!Trigger::eventWithTransaction(new DeleteProductCategory($code))) {
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
        if (!$info = ProductCategory::disable()->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->setAttribute('status', ProductCategory::STATUS_ENABLE)->save()) {
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
        if (!$info = ProductCategory::enable()->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->setAttribute('status', ProductCategory::STATUS_DISABLE)->save()) {
            return errors('保存失败');
        }
        return success([], '禁用成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSubList($code)
    {
        if (!$info = ProductCategory::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        return success($info->subCategory->where('status', ProductCategory::STATUS_ENABLE));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getEightSubProductCategory()
    {
        return success(ListQueryBuilder::create(ProductCategory::enable()
            ->where('recommend', ProductCategory::RECOMMEND_ENABLED)
            ->orderByDesc('order')->limit(10))
            ->getList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAllEnableList()
    {
        return success(ListQueryBuilder::create(ProductCategory::enable()->whereLevel(1)->with(['subCategory']))
            ->withOrder('order', 'asc')
            ->withHidden(['subCategory'])
            ->withAppends(['sub_enable_category'])
            ->getList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTopProductCategory()
    {
        return success(ProductCategory::query()->whereNull('parent_code')->get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getRecommendList()
    {
        return success(definition_to_select(ProductCategory::getRecommendLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTreeList()
    {
        return success(collect(ProductCategory::query()->whereNull('parent_code')->get()->toArray())->map(function ($category) {
            $category['subs'] = ProductCategory::whereParentCode($category['code'])->get()->toArray();
            return $category;
        }));

    }
}
