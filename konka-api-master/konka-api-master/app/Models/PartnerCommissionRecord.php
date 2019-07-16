<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;


/**
 * App\Models\PartnerCommissionRecord
 *
 * @property int $id
 * @property string $order_code 订单编号
 * @property int $type 佣金类型:1级、2级
 * @property float $order_amount 订单金额
 * @property float $order_pay_amount 订单实际支付金额
 * @property float $convert_ratio 换算比例1元兑多少K币
 * @property float $integral 产生K币
 * @property string $should_unlock_time 预计解冻时间
 * @property string|null $unlocked_at 实际解冻时间
 * @property string $partner_code 合伙人编码
 * @property int $status 佣金状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $code 编码
 * @property array $commission_composition 佣金组成方式
 * @property float $total_commission_amount 计算佣金总金额
 * @property-read mixed $status_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereCommissionComposition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereConvertRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereOrderCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereOrderPayAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord wherePartnerCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereShouldUnlockTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereTotalCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereUnlockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerCommissionRecord whereUpdatedAt($value)
 * @method static array getStatusLabels()
 * @method static array getStatusList()
 * @method static string getStatusLabel($key)
 * @mixin \Eloquent
 */
class PartnerCommissionRecord extends Model
{
    use DefinitionAttribute;

    /**
     * 佣金类型
     */

    const TYPE_FIRST_LEVEL = 10;//一级佣金
    const TYPE_SECOND_LEVEL = 20;//二级佣金

    /**
     * 佣金状态
     */

    const STATUS_WAIT_PAY = 5;//待支付
    const STATUS_WAIT_SETTLEMENT = 10;//待结算
    const STATUS_HAS_SETTLEMENT = 20;//已计算

    /**
     * @var array
     */

    protected $attributes = [
        'status' => self::STATUS_WAIT_PAY
    ];

    protected $casts = [
        'commission_composition' => 'array'
    ];

    protected $appends = [
        'status_text'
    ];

    /**
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_WAIT_PAY => '待订单完成',
            self::STATUS_WAIT_SETTLEMENT => '待结算',
            self::STATUS_HAS_SETTLEMENT => '已结算',
        ];
    }

    /**
     *
     */

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->integral = round($model->total_commission_amount * $model->convert_ratio, 2);
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 7, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($maxModel = self::orderByDesc('code')->first()) {
            return substr($maxModel['code'], strlen(self::codePrefix()));
        }
        return 0;
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'PCR';
    }

    /**
     * @param Builder $query
     * @return Builder
     */

    public function scopeActive(Builder $query)
    {
        return $query->whereIn('status', [self::STATUS_WAIT_SETTLEMENT, self::STATUS_HAS_SETTLEMENT]);
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return self::getStatusLabel($this->status);
    }
}
