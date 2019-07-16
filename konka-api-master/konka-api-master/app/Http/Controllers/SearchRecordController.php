<?php

namespace App\Http\Controllers;


use App\Models\SearchRecord;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;

class SearchRecordController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSearchRecord()
    {
        if (empty(auth_user())) {
            return success();
        }
        return success(ListQueryBuilder::create(SearchRecord::query()->whereUserCode(auth_user()->code)->limit(5))->getList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getPopularSearchRecord()
    {
        $record = \DB::select("SELECT content,COUNT(*) as count FROM `search_records` GROUP BY content ORDER BY count desc LIMIT 5");
        return success($record);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function deleteSearchRecord()
    {
        if (!SearchRecord::whereUserCode(auth_user()->code)->delete()) {
            return errors('清除失败');
        }
        return success();
    }
}