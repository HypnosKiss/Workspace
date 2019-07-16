<?php

namespace App\Http\Controllers;


use App\Models\SearchRecord;
use App\Models\User;
use App\Models\WxAppletsSession;
use App\UserEvents\User\UpdateInfoAvatar;
use App\UserEvents\User\WxAppletsLogin;
use App\UserEvents\User\WxOpenidLogin;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class UserController extends Controller
{
    /**
     * 获取小程序当前用户openId
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function getOpenId($code)
    {
        $session = app('wechat.mini_program')->auth->session($code);
        if (empty($session['openid'])) {
            return errors($session['errmsg']);
        }
        WxAppletsSession::setData(['openid' => $session['openid'], 'session_key' => $session['session_key']]);
        return success(['openid' => $session['openid']]);
    }

    /**
     * 微信小程序手机号码登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function wxAppletsLogin(Request $request)
    {
        if (!$result = Trigger::eventResult(new WxAppletsLogin($request->input('encryptData'), $request->input('iv')))) {
            return errors('登录失败');
        }
        return success($result);
    }

    /**
     * openId登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function wxOpenidLogin(Request $request)
    {
        if (!$result = Trigger::eventResult(new WxOpenidLogin($request->input()))) {
            return errors('登录失败');
        }
        return success($result);
    }

    /**
     * 用户列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(User::query())
            ->withSearch([
                [
                    'key' => 'code',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ], [
                    'key' => 'username',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ], [
                    'key' => 'phone',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ]
            ])->withPage()->paginateList());
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusEnable($code)
    {
        if (!$info = User::whereCode($code)->first()) {
            return errors('用户编码不存在');
        }
        if (!$info->setAttribute('status', User::STATUS_ENABLE)->save()) {
            return errors('修改失败');
        }
        return success([], '启用成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putStatusDisable($code)
    {
        if (!$info = User::whereCode($code)->first()) {
            return errors('用户编码不存在');
        }
        if (!$info->setAttribute('status', User::STATUS_DISABLE)->save()) {
            return errors('修改失败');
        }
        return success([], '禁用成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo()
    {
        return success(auth_user()->current());
    }

    /**
     * 微信受权修改用户头像
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function putInfoAvatar(Request $request)
    {
        if (!Trigger::eventWithTransaction(new UpdateInfoAvatar($request->input('encryptData'), $request->input('iv')))) {
            return errors('保存失败');
        }
        return success();
    }
}
