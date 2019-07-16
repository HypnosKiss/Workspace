<?php

namespace App\Http\Controllers;

use App\Models\CommissionRule;
use App\Models\PublicDefinition;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;

class CommissionRuleController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(CommissionRule::query())
            ->withSearch([
                [
                    'key' => 'name',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ]
            ])
            ->withPage()
            ->paginateList());
    }

    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'name' => ['required'],
            'order' => ['required', 'integer'],
            'beginTime' => ['required', 'date_format:Y-m-d H:i'],
            'endTime' => ['required', 'date_format:Y-m-d H:i', 'after:beginTime'],
            'firstLevelCommissionPercentage' => ['required', 'numeric', 'min:0'],
            'secondLevelCommissionPercentage' => ['required', 'numeric', 'min:0'],
            'partners' => ['required', 'array', 'min:1'],
            'products' => ['required', 'array', 'min:1'],
            'partners.*' => ['distinct', Rule::exists('tags', 'code')->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query->where('type', Tag::TYPE_PARTNER)->where('status', PublicDefinition::STATUS_ENABLED);
            })],
            'products.*' => ['distinct', Rule::exists('tags', 'code')->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query->where('type', Tag::TYPE_PRODUCT)->where('status', PublicDefinition::STATUS_ENABLED);
            })],
        ];
    }

    /**
     * @return array
     */

    protected function messages()
    {
        return [
            'name.required' => '规则名称不能为空',
            'order.required' => '优先级不能为空',
            'order.integer' => '优先级必须为整数',
            'beginTime.required' => '开始时间不能为空',
            'beginTime.date_format' => '开始时间的时间格式不正确',
            'endTime.required' => '结束时间不能为空',
            'endTime.date_format' => '结束时间的时间格式不正确',
            'endTime.after' => '结束时间必须晚于开始时间',
            'firstLevelCommissionPercentage.required' => '一级佣金比例不能为空',
            'firstLevelCommissionPercentage.numeric' => '一级佣金比例必须是数值',
            'firstLevelCommissionPercentage.min' => '一级佣金比例必须大于等于0',
            'secondLevelCommissionPercentage.required' => '二级佣金比例不能为空',
            'secondLevelCommissionPercentage.numeric' => '二级佣金比例必须是数值',
            'secondLevelCommissionPercentage.min' => '二级佣金比例必须大于等于0',
            'partners.required' => '至少选择一项合伙人标签',
            'partners.array' => '合伙人标签必须是数组类型',
            'partners.min' => '至少选择一项合伙人标签',
            'products.required' => '至少选择一项商品标签',
            'products.array' => '商品标签必须是数组类型',
            'products.min' => '至少选择一项商品标签',
            'partners.*.exists' => '合伙人标签存在无效选项，请刷新页面再试',
            'partners.*.distinct' => '合伙人标签不能重复',
            'products.*.exists' => '商品标签存在无效选项，请刷新页面再试',
            'products.*.distinct' => '商品标签不能重复',
        ];
    }

    /**
     * @return array
     */

    protected function fillKeys()
    {
        return [
            'name', 'order', 'beginTime', 'endTime', 'firstLevelCommissionPercentage',
            'secondLevelCommissionPercentage', 'partners', 'products'
        ];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        $this->validate($request, $this->rules(), $this->messages());
        if (!(new CommissionRule())
            ->fillable(snake_case_array($this->fillKeys()))
            ->fill(snake_case_array_keys($request->only($this->fillKeys())))
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
        if (!$model = CommissionRule::whereCode($code)->first()) {
            return errors('找不到规则');
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
        if (!$model = CommissionRule::whereCode($code)->first()) {
            return errors('找不到规则');
        }
        $this->validate($request, $this->rules(), $this->messages());
        if (!$model
            ->fillable(snake_case_array($this->fillKeys()))
            ->fill(snake_case_array_keys($request->only($this->fillKeys())))
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
        if (!$model = CommissionRule::whereCode($code)->first()) {
            return errors('找不到规则');
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

    public function putDisabled($code)
    {
        if (!$model = CommissionRule::whereCode($code)->first()) {
            return errors('找不到规则');
        }
        if (!$model->toDisabled()) {
            return errors('禁用失败');
        }
        return success([], '禁用成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putEnabled($code)
    {
        if (!$model = CommissionRule::whereCode($code)->first()) {
            return errors('找不到规则');
        }
        if (!$model->toEnabled()) {
            return errors('启用失败');
        }
        return success([], '启用成功');
    }
}
