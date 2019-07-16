<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerCommissionRecord;
use App\Models\PartnerTag;
use App\UserEvents\Partner\AccountLogin;
use App\UserEvents\Partner\ActiveCodeRegister;
use App\UserEvents\Partner\BatchImport;
use App\UserEvents\Partner\CreatePartner;
use App\UserEvents\Partner\Logout;
use App\UserEvents\Partner\MobileLogin;
use App\UserEvents\Partner\ResetPassword;
use App\UserEvents\Partner\UpdatePartner;
use App\UserEvents\Partner\UpdateTags;
use App\UserEvents\Partner\WxPhoneLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use ZhiEq\Contracts\Controller;
use ZhiEq\Exceptions\CustomException;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class PartnerController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Partner::query())
            ->withSearch([
                [
                    'key' => 'type',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH,
                ],
                [
                    'key' => 'code',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ],
                [
                    'key' => ['r3Code' => 'r3_code'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['clientName' => 'client_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['clientType' => 'client_type'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['area' => 'area'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['mergeCode' => 'merge_code'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['salesman' => 'salesman'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['salesmanPhone' => 'salesman_phone'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['companyName' => 'company_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['activationCode' => 'activation_code'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['companyAddress' => 'company_address'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['province' => 'province'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['city' => 'city'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['county' => 'county'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['idName' => 'id_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['idNumber' => 'id_number'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['inlineName' => 'inline_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['inlineNumber' => 'inline_number'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['firstDepartment' => 'first_department'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['secondDepartment' => 'second_department'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['thirdDepartment' => 'third_department'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['handingName' => 'handing_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['networkName' => 'network_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['networkCode' => 'network_code'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['parentClientName' => 'parent_client_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['parentClientCode' => 'parent_client_code'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['town' => 'town'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
            ])
            ->withPage()
            ->withAppends(['status_text', 'type_text'])
            ->paginateList());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreatePartner($request->input()))) {
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
        if (!$info = Partner::whereCode($code)->first()) {
            return errors('编码不存在');
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
        if (!$info = Partner::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!Trigger::eventWithTransaction(new UpdatePartner($info, $request->input()))) {
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
        if (!$info = Partner::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->toDelete()) {
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
        if (!$info = Partner::whereCode($code)->first()) {
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

    public function putDisabled($code)
    {
        if (!$info = Partner::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->toDisabled()) {
            return errors('禁用失败');
        }
        return success([], '禁用成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putDowngrade($code)
    {
        if (!$info = Partner::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->toDowngrade()) {
            return errors('降级失败');
        }
        return success([], '降级成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTypeList()
    {
        return success(definition_to_select(Partner::getTypeLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getStatusList()
    {
        return success(definition_to_select(Partner::getStatusLabels()));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function batchImport(Request $request)
    {
        if (!Trigger::eventWithTransaction(new BatchImport($request->input()))) {
            return errors('导入失败');
        }
        return success([], '导入成功');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postMobileLogin(Request $request)
    {
        if (!$result = Trigger::eventWithTransaction(new MobileLogin($request->input()))) {
            return errors('登录失败');
        }
        return success($result, '登录成功');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postAccountLogin(Request $request)
    {
        if (!$result = Trigger::eventWithTransaction(new AccountLogin($request->input()))) {
            return errors('登录失败');
        }
        return success($result, '登录成功');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postWxPhoneLogin(Request $request)
    {
        if (!$result = Trigger::eventWithTransaction(new WxPhoneLogin($request->input()))) {
            return errors('登录失败');
        }
        return success($result, '登录成功');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postActiveCodeRegister(Request $request)
    {
        if (!$result = Trigger::eventWithTransaction(new ActiveCodeRegister($request->input()))) {
            return errors('激活失败');
        }
        return success($result, '激活成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getActiveCodeName($code)
    {
        if (!$info = Partner::whereActivationCode($code)->first()) {
            return errors('激活码无效');
        }
        if ($info->type === Partner::TYPE_R3_CLIENT) {
            return empty($info->client_name) ? errors('R3客户账号异常') : success(['clientName' => $info->client_name]);
        } elseif ($info->type === Partner::TYPE_NETWORK_CLIENT) {
            return empty($info->network_name) ? errors('网点客户账号异常') : success(['clientName' => $info->network_name]);
        } elseif ($info->type === Partner::TYPE_INTERNAL_STAFF) {
            return empty($info->inline_name) ? errors('员工账号异常') : success(['clientName' => $info->inline_name . '-' . $info->third_department]);
        } elseif ($info->type === Partner::TYPE_COOPERATION) {
            return empty($info->inline_name) ? errors('合作账号异常') : success(['clientName' => $info->inline_name . '-' . $info->first_department]);
        } else {
            return errors('账号无法激活，请联系管理员');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSaleCommissionOrders()
    {
        return success(ListQueryBuilder::create(PartnerCommissionRecord::active()
            ->orderByDesc('created_at')
            ->where('partner_code', auth_user()->partner_code)
        )
            ->withSearch([
                [
                    'key' => ['orderCode' => 'order_code'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ]
            ])
            ->withPage()
            ->paginateList());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSales(Request $request)
    {
        return success((int)$request->header('x-total-type') === 10 ? $this->getMonthSale($request) : $this->getYearSale($request));
    }

    /**
     * @param Request $request
     * @return array
     */

    protected function getMonthSale(Request $request)
    {
        $searchKeyword = empty($request->header('X-Search-Keywords')) ? [] : json_decode(base64_decode($request->header('X-Search-Keywords')), true);
        list($beginTime, $endTime) = empty($searchKeyword['time']) ? [Carbon::now()->addMonths(-11)->format('Y-m-01'), Carbon::now()->format('Y-m-01')] : $searchKeyword['time'];
        $months = 0;
        $times = [];
        $beginMonth = new Carbon($beginTime);
        $endMonth = (new Carbon($endTime))->addMonth();
        while ($endMonth->diffInDays($beginMonth) > 0) {
            $months++;
            $times[] = [$beginMonth->format('Y-m-d'), $beginMonth->copy()->addMonth()->format('Y-m-d')];
            $beginMonth->addMonth();
        }
        if ($months <= 0) {
            throw new CustomException('结束日期必须大于开始日期');
        }
        $subPartners = Partner::whereParentCode(auth_user()->partner_code)->get()->pluck('code');
        return collect(array_reverse($times))->map(function ($time) use ($subPartners) {
            return [
                'mySales' => PartnerCommissionRecord::active()
                    ->where('partner_code', auth_user()->partner_code)
                    ->where('created_at', '>=', $time[0])
                    ->where('created_at', '<=', $time[1])
                    ->sum('order_pay_amount'),
                'subSales' => PartnerCommissionRecord::active()
                    ->whereIn('partner_code', $subPartners)
                    ->where('created_at', '>=', $time[0])
                    ->where('created_at', '<=', $time[1])
                    ->sum('order_pay_amount'),
                'time' => (new Carbon($time[0]))->format('Y-m')
            ];
        })->toArray();
    }

    /**
     * @param Request $request
     * @return array
     */

    protected function getYearSale(Request $request)
    {
        $searchKeyword = empty($request->header('X-Search-Keywords')) ? [] : json_decode(base64_decode($request->header('X-Search-Keywords')), true);
        list($beginTime, $endTime) = empty($searchKeyword['time']) ? [Carbon::now()->addYears(-5)->format('Y'), Carbon::now()->format('Y')] : $searchKeyword['time'];
        $years = (new Carbon($endTime . '-01-01'))->diffInYears(new Carbon($beginTime . '-01-01'));
        if ($years <= 0) {
            throw new CustomException('结束日期必须大于开始日期');
        }
        $begin = new Carbon($beginTime . '-01-01');
        $times = [];
        for ($i = 0; $i < ($years + 1); $i++) {
            $times[] = [(clone $begin)->addYears($i)->format('Y-m-d'), (clone $begin)->addYears($i + 1)->format('Y-m-d')];
        }
        $subPartners = Partner::whereParentCode(auth_user()->partner_code)->get()->pluck('code');
        return collect(array_reverse($times))->map(function ($time) use ($subPartners) {
            return [
                'mySales' => PartnerCommissionRecord::active()
                    ->where('partner_code', auth_user()->partner_code)
                    ->where('created_at', '>=', $time[0])
                    ->where('created_at', '<=', $time[1])
                    ->sum('order_pay_amount'),
                'subSales' => PartnerCommissionRecord::active()
                    ->whereIn('partner_code', $subPartners)
                    ->where('created_at', '>=', $time[0])
                    ->where('created_at', '<=', $time[1])
                    ->sum('order_pay_amount'),
                'time' => (new Carbon($time[0]))->format('Y')
            ];
        })->toArray();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSaleCommissionRecords()
    {
        return success(ListQueryBuilder::create(PartnerCommissionRecord::active()->where('partner_code', auth_user()->partner_code))
            ->withSearch([
                [
                    'key' => ['time' => 'created_at'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_DATE_BETWEEN,
                ],
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH,
                ]
            ])
            ->withPage()
            ->paginateList());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSaleCommissionOrderTotal()
    {
        return success([
            "orderTotal" => PartnerCommissionRecord::active()->where('partner_code', auth_user()->partner_code)->count(),
            "salesAmountTotal" => PartnerCommissionRecord::active()->where('partner_code', auth_user()->partner_code)->sum('order_pay_amount'),
            "integralTotal" => PartnerCommissionRecord::active()->where('partner_code', auth_user()->partner_code)->sum('integral')
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSaleTotal()
    {
        $total = [
            "monthSales" => 0,
            "monthSubSales" => 0,
            "yearSales" => 0,
            "yearSubSales" => 0
        ];
        if (empty(auth_user()->partner)) {
            return success($total);
        }
        //
        $baseSalesQuery = PartnerCommissionRecord::active()
            ->where('partner_code', auth_user()->partner_code);
        $total['monthSales'] = $baseSalesQuery
            ->where('created_at', '>=', Carbon::now()->firstOfMonth())
            ->where('created_at', '<=', Carbon::now()->endOfMonth())
            ->sum('order_pay_amount');
        $total['yearSales'] = $baseSalesQuery
            ->where('created_at', '>=', Carbon::now()->firstOfYear())
            ->where('created_at', '<=', Carbon::now()->endOfYear())
            ->sum('order_pay_amount');
        //
        $subPartners = Partner::whereParentCode(auth_user()->partner_code)->get()->pluck('code');
        $baseSubSalesQuery = PartnerCommissionRecord::active()
            ->whereIn('partner_code', $subPartners);
        $total['monthSubSales'] = $baseSubSalesQuery
            ->where('created_at', '>=', Carbon::now()->firstOfMonth())
            ->where('created_at', '<=', Carbon::now()->endOfMonth())
            ->sum('order_pay_amount');
        $total['yearSubSales'] = $baseSubSalesQuery
            ->where('created_at', '>=', Carbon::now()->firstOfYear())
            ->where('created_at', '<=', Carbon::now()->endOfYear())
            ->sum('order_pay_amount');
        return success($total);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSaleCommissionRecordTotal()
    {
        return success([
            "totalKMoney" => empty(auth_user()->partner) ? 0 : auth_user()->partner->total_get_integral,
            "availableKMoney" => empty(auth_user()->partner) ? 0 : auth_user()->partner->available_integral,
            "hasWithdrawKMoney" => empty(auth_user()->partner) ? 0 : auth_user()->partner->has_withdraw_integral,
            "waitSettlementKMoney" => empty(auth_user()->partner) ? 0 : auth_user()->partner->lock_integral,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function putLogout()
    {
        if (!$result = Trigger::eventWithTransaction(new Logout())) {
            return errors('退出失败');
        }
        return success($result, '退出成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTags($code)
    {
        if (!$product = Partner::whereCode($code)->first()) {
            return errors('合伙人不存在');
        }
        return success(PartnerTag::wherePartnerCode($code)->get()->pluck('tag_code'));
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function putTags($code, Request $request)
    {
        if (!Trigger::eventWithTransaction(new UpdateTags($code, $request->input()))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Exception
     */

    public function exportList(Request $request)
    {
        /**
         * @var Partner[] $orders
         */
        $partners = empty($request->header('X-Select-Codes', [])) ?
            ListQueryBuilder::create(Partner::query())
                ->withSearch([
                    [
                        'key' => 'type',
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH,
                    ],
                    [
                        'key' => 'code',
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => 'status',
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                    ],
                    [
                        'key' => ['r3Code' => 'r3_code'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['clientName' => 'client_name'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['clientType' => 'client_type'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['area' => 'area'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['mergeCode' => 'merge_code'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['salesman' => 'salesman'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['salesmanPhone' => 'salesman_phone'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['companyName' => 'company_name'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['activationCode' => 'activation_code'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['companyAddress' => 'company_address'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['province' => 'province'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['city' => 'city'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['county' => 'county'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['idName' => 'id_name'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['idNumber' => 'id_number'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['inlineName' => 'inline_name'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['inlineNumber' => 'inline_number'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['firstDepartment' => 'first_department'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['secondDepartment' => 'second_department'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['thirdDepartment' => 'third_department'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['handingName' => 'handing_name'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['networkName' => 'network_name'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['networkCode' => 'network_code'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['parentClientName' => 'parent_client_name'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['parentClientCode' => 'parent_client_code'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                    [
                        'key' => ['town' => 'town'],
                        'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                    ],
                ])->withAppends(['status_text', 'type_text'])->get() :
            Partner::whereIn('code', json_decode(base64_decode($request->header('X-Select-Codes')), true))->get();
        $title = [
            '合伙人编码',
            '合伙人类型',
            '激活码',
            '联系电话',
            '真实姓名',
            '身份证号码',
            '省',
            '市',
            '县',
            '乡/镇',
            '客户地址',
            '员工姓名',
            '电话/工卡编号',
            '一级部门',
            '二级部门',
            '三级部门',
            '分公司',
            'R3编码',
            '客户名称',
            '客户类型',
            '区域信息',
            '合并编码',
            '业务员姓名',
            '业务员电话',
            '经办名称',
            '网点编号',
            '网点名称',
            '上级客户',
            '上级客户编码',
            '状态'
        ];
        $titleMaps = [
            'code', 'type_text', 'activation_code', 'client_phone', 'id_name', 'id_number',
            'province', 'city', 'county', 'town', 'company_address', 'inline_name', 'inline_number',
            'first_department', 'second_department', 'third_department', 'company_name', 'r3_code',
            'client_name', 'client_type', 'area', 'merge_code', 'salesman', 'salesman_phone',
            'handing_name', 'network_code', 'network_name', 'parent_client_name', 'parent_client_code', 'status_text'
        ];
        return success(['fileUrl' => export_excel(Uuid::uuid4(), $partners, $title, $titleMaps)]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function resetPassword(Request $request)
    {
        if (!Trigger::eventWithTransaction(new ResetPassword($request->input()))) {
            return errors('重置失败');
        }
        return success([], '重置成功');
    }

}
