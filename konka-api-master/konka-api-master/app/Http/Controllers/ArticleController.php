<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;

class ArticleController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Article::query()->orderByDesc('created_at'))
            ->withSearch([
                [
                    'key' => 'title',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
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
            'title' => ['required'],
            'content' => ['array', 'min:1']
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'content.array' => '内容必须为数组',
            'content.min' => '内容最少上传一张图片',
        ];
        $this->validate($request, $rules, $messages);
        if (count(array_filter($request->input('content'), function ($image) {
                return !empty($image['image']);
            })) <= 0) {
            return errors('最少上传一张内容图');
        }
        if (!(new Article())
            ->fillable(array_keys($rules))
            ->fill($request->only(array_keys($rules)))
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
        if (!$info = Article::whereCode($code)->first()) {
            return errors('文章不存在');
        }
        return success($info->toArray());
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo($code, Request $request)
    {
        if (!$info = Article::whereCode($code)->first()) {
            return errors('文章不存在');
        }
        $rules = [
            'title' => ['required'],
            'content' => ['array', 'min:1']
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'content.array' => '内容必须为数组',
            'content.min' => '内容最少上传一张图片',
        ];
        $this->validate($request, $rules, $messages);
        if (count(array_filter($request->input('content'), function ($image) {
                return !empty($image['image']);
            })) <= 0) {
            return errors('最少上传一张内容图');
        }
        if (!$info
            ->fillable(array_keys($rules))
            ->fill($request->only(array_keys($rules)))
            ->save()
        ) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function deleteInfo($code)
    {
        if (!$info = Article::whereCode($code)->first()) {
            return errors('文章不存在');
        }
        if (!$info->delete()) {
            return errors('删除失败');
        }
        return success([], '删除成功');
    }

}
