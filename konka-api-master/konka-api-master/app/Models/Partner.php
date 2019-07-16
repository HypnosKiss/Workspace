<?php

namespace App\Models;

use App\ModelEvents\Partner\DeletePartner;
use App\ModelEvents\Partner\DisabledPartner;
use App\ModelEvents\Partner\DowngradePartner;
use App\ModelEvents\Partner\EnabledPartner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;
use ZhiEq\Utils\Trigger;


/**
 * App\Models\Partner
 *
 * @property int $id
 * @property string $code 编码
 * @property string|null $user_code 用户编码
 * @property string|null $parent_code 上级合伙人编码
 * @property string $client_name 名称
 * @property string|null $client_phone 客户电话
 * @property int $status 状态
 * @property string|null $company_name 分公司
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $type 一类、二类合伙人
 * @property string|null $area 区域
 * @property string $invite_code 邀请码
 * @property string|null $activation_code 激活码
 * @property string|null $r3_code R3编码
 * @property string|null $merge_code 合并编码
 * @property string|null $client_type 客户类型
 * @property string|null $company_address 公司地址
 * @property string|null $salesman 业务员
 * @property string|null $salesman_phone 业务员电话
 * @property float $total_integral 总积分(K币)
 * @property float $lock_integral 冻结积分(K币)
 * @property float $has_withdraw_integral 已提现积分(K币)
 * @property string|null $password
 * @property int|null $hasShop
 * @property int|null $career
 * @property int|null $businessCategory
 * @property string|null $province 省
 * @property string|null $city 市
 * @property string|null $county 县
 * @property string|null $id_name 真实姓名
 * @property string|null $id_number 身份证号码
 * @property string|null $id_status 实名认证状态
 * @property string|null $inline_name 内部人员姓名
 * @property string|null $inline_number 内部人员编码
 * @property string|null $first_department 一级部门
 * @property string|null $second_department 二级部门
 * @property string|null $third_department 三级部门
 * @property string|null $handing_name 经办名称
 * @property string|null $network_name 网点名称
 * @property string|null $network_code 网点编号
 * @property string|null $parent_client_name 上级客户名称
 * @property string|null $parent_client_code 上级客户编码
 * @property-read float $available_integral
 * @property-read int $month_sales_number
 * @property-read int $sale_orders_number
 * @property-read mixed $status_text
 * @property-read int $team_count
 * @property-read float $total_get_integral
 * @property-read string $type_text
 * @property-read int $year_sales_number
 * @method static array getTypeLabels()
 * @method static array getTypeList()
 * @method static string getTypeLabel($key)
 * @method static array getStatusLabels()
 * @method static array getStatusList()
 * @method static string getStatusLabel($key)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner disabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereBusinessCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereCareer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereClientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereCompanyAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereFirstDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereHandingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereHasShop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereHasWithdrawIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereIdName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereIdStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereInlineName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereInlineNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereInviteCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereLockIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereMergeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereNetworkCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereNetworkName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereParentClientCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereParentClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereParentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereR3Code($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereSalesman($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereSalesmanPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereSecondDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereThirdDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereTotalIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereUserCode($value)
 * @mixin \Eloquent
 * @property string|null $town 乡镇
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Partner whereTown($value)
 * @property-read mixed $nickname
 * @property-read \App\Models\User|null $user
 */
class Partner extends Model
{
    use DefinitionAttribute;

    /**
     * 合伙人类型定义
     */

    const TYPE_EXTERNAL = 10;
    const TYPE_R3_CLIENT = 20;
    const TYPE_INTERNAL_STAFF = 30;
    const TYPE_COOPERATION = 40;
    const TYPE_NETWORK_CLIENT = 50;

    /**
     * @return array
     */

    protected static function typeDefinition()
    {
        return [
            self::TYPE_R3_CLIENT => 'R3客户',
            self::TYPE_INTERNAL_STAFF => '内部员工',
            self::TYPE_COOPERATION => '合作企业',
            self::TYPE_NETWORK_CLIENT => '网点客户',
            self::TYPE_EXTERNAL => '二类合伙人'
        ];
    }

    /**
     * @return array
     */

    public static function importType()
    {
        return [
            self::TYPE_R3_CLIENT,
            self::TYPE_INTERNAL_STAFF,
            self::TYPE_NETWORK_CLIENT,
            self::TYPE_COOPERATION
        ];
    }

    const STATUS_ENABLED = 10;
    const STATUS_DISABLED = 20;
    const STATUS_IN_ACTIVE = 30;

    protected static function statusDefinition()
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
            self::STATUS_IN_ACTIVE => '未激活'
        ];
    }


    protected $attributes = [
        'status' => self::STATUS_IN_ACTIVE,
    ];

    protected $appends = [
        'status_text', 'type_text'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->invite_code = CodeGenerator::getUniqueCode('PartnerInviteCode', function () {
                return self::inviteMaxCode();
            }, 5, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::inviteCodePrefix(), 0);
            if (in_array($model->type, self::importType()) && empty($model->user_code)) {
                do {
                    $activeCode = rand(000000, 999999);
                } while (self::whereActivationCode($activeCode)->exists());
                $model->activation_code = !empty($model->activation_code) ? $model->activation_code : $activeCode;
            }
        });
        self::saving(function (self $model) {
            if (!$model->exists) {
                $model->code = CodeGenerator::getUniqueCode(self::class . ':' . $model->type, function () use ($model) {
                    return self::maxCode($model->type);
                }, 6, CodeGenerator::TYPE_ONLY_NUMBER, self::codePrefix($model->type), 0, 9);
            } elseif ($model->isDirty('type')) {
                $model->code = CodeGenerator::getUniqueCode(self::class . ':' . $model->type, function () use ($model) {
                    return self::maxCode($model->type);
                }, 6, CodeGenerator::TYPE_ONLY_NUMBER, self::codePrefix($model->type), 0, 9);
                if (self::whereParentCode($model->getOriginal('code'))->update(['code' => $model->code]) === false) {
                    return false;
                }
                if (User::wherePartnerCode($model->getOriginal('code'))->update(['code' => $model->code]) === false) {
                    return false;
                }
            }
        });
    }

    /**
     * @return string
     */

    protected static function inviteCodePrefix()
    {
        return 'PI';
    }

    /**
     * @return bool|int|string
     */

    protected static function inviteMaxCode()
    {
        if ($maxModel = self::orderByDesc('invite_code')->first()) {
            return substr($maxModel['invite_code'], strlen(self::inviteCodePrefix()));
        }
        return 0;
    }

    /**
     * @param $type
     * @return string
     */

    protected static function codePrefix($type)
    {
        return $type === in_array($type, self::importType()) ? 'K1' : 'K2';
    }

    /**
     * @param $type
     * @return bool|int|string
     */

    protected static function maxCode($type)
    {
        if ($maxModel = self::whereIn('type', in_array($type, self::importType()) ? self::importType() : [$type])->orderByDesc('code')->first()) {
            return substr($maxModel['code'], strlen(self::codePrefix($type)));
        }
        return 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'code');
    }

    /**
     * @param $value
     * @return Partner
     */

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
        return $this;
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return self::getStatusLabel($this->status);
    }

    /**
     * @return string
     */

    public function getTypeTextAttribute()
    {
        return self::getTypeLabel($this->type);
    }

    /**
     * @return float
     */

    public function getTotalGetIntegralAttribute()
    {
        return round($this->total_integral + $this->has_withdraw_integral, 2);
    }

    /**
     * @return string|null
     */

    public function getNicknameAttribute()
    {
        return empty($this->user) ? '' : $this->user->nickname;
    }

    /**
     * @return float
     */

    public function getAvailableIntegralAttribute()
    {
        return round($this->total_integral - $this->lock_integral, 2);
    }

    /**
     * @return int
     */

    public function getTeamCountAttribute()
    {
        return self::whereParentCode($this->code)->count();
    }

    /**
     * @return int
     */

    public function getSaleOrdersNumberAttribute()
    {
        return PartnerCommissionRecord::active()->wherePartnerCode($this->code)->count();
    }

    /**
     * @return int
     */

    public function getMonthSalesNumberAttribute()
    {
        return PartnerCommissionRecord::active()->wherePartnerCode($this->code)
            ->where('created_at', '>=', Carbon::now()->firstOfMonth())
            ->where('created_at', '<=', Carbon::now()->endOfMonth())
            ->sum('order_pay_amount');
    }

    /**
     * @return int
     */

    public function getYearSalesNumberAttribute()
    {
        return PartnerCommissionRecord::active()->wherePartnerCode($this->code)
            ->where('created_at', '>=', Carbon::now()->firstOfYear())
            ->where('created_at', '<=', Carbon::now()->endOfYear())
            ->sum('order_pay_amount');
    }

    /**
     * @param Builder $query
     * @return mixed
     */

    public function scopeEnabled($query)
    {
        return $query->where('status', PublicDefinition::STATUS_ENABLED);
    }

    /**
     * @param Builder $query
     * @return mixed
     */

    public function scopeDisabled($query)
    {
        return $query->where('status', PublicDefinition::STATUS_ENABLED);
    }

    /**
     * @param $password
     * @return bool
     */

    public function validatePassword($password)
    {
        return empty($password) ? false : \Hash::check($password, $this->password);
    }

    /**
     * @return bool
     */

    public function toDelete()
    {
        return Trigger::eventWithTransaction(new DeletePartner($this));
    }

    /**
     * @return bool
     */

    public function toDisabled()
    {
        return Trigger::eventWithTransaction(new DisabledPartner($this));
    }

    /**
     * @return bool
     */

    public function toEnabled()
    {
        return Trigger::eventWithTransaction(new EnabledPartner($this));
    }

    /**
     * @return mixed
     */

    public function toDowngrade()
    {
        return Trigger::eventWithTransaction(new DowngradePartner($this));
    }
}
