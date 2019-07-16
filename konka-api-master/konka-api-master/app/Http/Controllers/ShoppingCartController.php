<?php

namespace App\Http\Controllers;


use App\Models\ShoppingCart;
use App\UserEvents\ShoppingCart\BatchDelete;
use App\UserEvents\ShoppingCart\CreateShoppingCart;
use App\UserEvents\ShoppingCart\UpdateShoppingCart;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\Trigger;

class ShoppingCartController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(ShoppingCart::whereUserCode(auth_user()->code))
            ->withAppends([
                'name', 'price', 'image_url', 'product_specification_text'
            ])->getList());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateShoppingCart($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function batchDelete(Request $request)
    {
        if (!Trigger::eventWithTransaction(new BatchDelete($request->input()))) {
            return errors('删除失败');
        }
        return success();
    }

    /**
     * 修改
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo(Request $request, $id)
    {
        if (!Trigger::eventWithTransaction(new UpdateShoppingCart($request->input(), $id))) {
            return errors('保存失败');
        }
        return success();
    }
}