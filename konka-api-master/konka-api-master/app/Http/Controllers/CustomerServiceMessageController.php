<?php

namespace App\Http\Controllers;


use App\Models\CustomerServiceMessage;
use App\Models\CustomServiceClient;
use App\UserEvents\CustomerServiceMessage\CreateCustomerServiceMessage;
use App\UserEvents\CustomerServiceMessage\ReplyCustomerServiceMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Exceptions\CustomException;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\Trigger;

class CustomerServiceMessageController extends Controller
{
    /**
     * 管理后台获取所有留言
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAllList()
    {
        return success(ListQueryBuilder::create(CustomerServiceMessage::query()->orderByDesc('created_at'))
            ->withPage()
            ->withAppends([

            ])
            ->paginateList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getUserList()
    {
        return success(ListQueryBuilder::create(CustomerServiceMessage::whereUserCode(auth_user()->code))
            ->withOrder('created_at', ListQueryBuilder::ORDER_TYPE_DESC)->withPage()->paginateList());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateCustomerServiceMessage($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 回复留言
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postReplyInfo(Request $request, $id)
    {
        if (!Trigger::eventWithTransaction(new ReplyCustomerServiceMessage($request->input(), $id))) {
            throw new CustomException('保存失败');
        }
        return success();
    }

    /**
     * 删除
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function deleteInfo($id)
    {
        if (!$info = CustomerServiceMessage::whereId($id)->first()) {
            return errors('ID无效');
        }
        if (!$info->delete()) {
            return errors('删除失败');
        }
        return success();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getClientList()
    {
        return success(CustomServiceClient::orderByDesc('last_send_at')
            ->where('last_send_at', '>', Carbon::now()->addDays(-15))
            ->get());
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function clearUnreadNum($code)
    {
        if (!$client = CustomServiceClient::whereUserCode($code)->first()) {
            return errors('不存在的客服客户');
        }
        if (!$client->clearUnread()) {
            return errors('清除未读数量失败');
        }
        return success([], '清除未读数量成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getClientMessageList($code)
    {
        return success(CustomerServiceMessage::whereUserCode($code)->limit(500)->orderBy('created_at', 'asc')->get());
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function postClientMessage($code, Request $request)
    {
        if (!$client = CustomServiceClient::whereUserCode($code)->first()) {
            return errors('不存在的客服客户');
        }
        if (!(new CustomerServiceMessage())->setAttribute('type', CustomerServiceMessage::TYPE_ADMIN)
            ->setAttribute('content', $request->input('content'))
            ->setAttribute('user_code', $code)
            ->setAttribute('message_type', CustomerServiceMessage::MESSAGE_TYPE_STRING)
            ->setAttribute('admin_code', auth_admin()->code)
            ->save()) {
            return errors('发送失败');
        }
        return success([], '发送成功');
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function postClientImage($code, Request $request)
    {
        if (!$client = CustomServiceClient::whereUserCode($code)->first()) {
            return errors('不存在的客服客户');
        }
        if (!(new CustomerServiceMessage())->setAttribute('type', CustomerServiceMessage::TYPE_ADMIN)
            ->setAttribute('content', $request->input('content'))
            ->setAttribute('user_code', $code)
            ->setAttribute('message_type', CustomerServiceMessage::MESSAGE_TYPE_IMAGE)
            ->setAttribute('admin_code', auth_admin()->code)
            ->save()) {
            return errors('发送失败');
        }
        return success([], '发送成功');
    }
}
