<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;

class TagController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Tag::query())
            ->withSearch([
                [
                    'key' => 'type',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ],
                [
                    'key' => 'code',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => 'name',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ]
            ])
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
        $rules = [
            'name' => ['required'],
            'type' => ['required', Rule::in(Tag::getTypeList())]
        ];
        $messages = [
            'name.required' => '标签名称不能为空',
            'type.required' => '标签类型不能为空',
            'type.in' => '标签类型不正确',
        ];
        $this->validate($request, $rules, $messages);
        $fillKey = array_keys($rules);
        if (!(new Tag())->fillable(snake_case_array($fillKey))
            ->fill(snake_case_array_keys($request->only($fillKey)))
            ->save()
        ) {
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
        if (!$model = Tag::whereCode($code)->first()) {
            return errors('找不到标签');
        }
        return success($model->toArray());
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo($code, Request $request)
    {
        if (!$model = Tag::whereCode($code)->first()) {
            return errors('找不到标签');
        }
        $rules = [
            'name' => ['required'],
            'type' => ['required', Rule::in(Tag::getTypeList())]
        ];
        $messages = [
            'name.required' => '标签名称不能为空',
            'type.required' => '标签类型不能为空',
            'type.in' => '标签类型不正确',
        ];
        $this->validate($request, $rules, $messages);
        $fillKey = array_keys($rules);
        if (!$model->fillable(snake_case_array($fillKey))
            ->fill(snake_case_array_keys($request->only($fillKey)))
            ->save()
        ) {
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
        if (!$model = Tag::whereCode($code)->first()) {
            return errors('找不到标签');
        }
        if (!$model->toDelete()) {
            return errors('删除失败');
        }
        return success([], '删除成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putEnabled($code)
    {
        if (!$model = Tag::whereCode($code)->first()) {
            return errors('找不到标签');
        }
        if (!$model->toEnabled()) {
            return errors('启用失败');
        }
        return success([], '启用成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putDisabled($code)
    {
        if (!$model = Tag::whereCode($code)->first()) {
            return errors('找不到标签');
        }
        if (!$model->toDisabled()) {
            return errors('禁用失败');
        }
        return success([], '禁用成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTypeList()
    {
        return success(definition_to_select(Tag::getTypeLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getProductList()
    {
        return success(Tag::onlyProduct()->enabled()->get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getPartnerList()
    {
        return success(Tag::onlyPartner()->enabled()->get());
    }
}
