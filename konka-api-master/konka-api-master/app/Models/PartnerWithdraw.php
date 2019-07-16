<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\PartnerWithdraw
 *
 * @property int $id
 * @property string $partner_code 合伙人编码
 * @property string $partner_name 合伙人名称
 * @property string $partner_phone 合伙人电话
 * @property float $price 提现金额
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw wherePartnerCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw wherePartnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw wherePartnerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @property string $code
 * @property-read mixed $status_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerWithdraw whereCode($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ApprovalRecord[] $approvalRecord
 * @property-read \App\Models\Partner $partner
 */
class PartnerWithdraw extends Model
{
    use DefinitionAttribute;

    const STATUS_BUSINESS_APPROVAL = 10;   //业务审核中
    const STATUS_FINANCIAL_APPROVAL = 20;  //财务审核中
    const STATUS_FINANCIAL_APPROVAL_FAILURE = 30;  //财务审核失败
    const STATUS_APPROVAL_FAILURE = 40;   //审核失败
    const STATUS_APPROVAL_SUCCESS = 50;   //审核成功

    protected $appends = [
        'status_text'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->code = self::generateCode();
            $model->status = self::STATUS_BUSINESS_APPROVAL;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function partner()
    {
        return $this->hasOne('App\Models\Partner', 'code', 'partner_code');
    }

    /**
     * 最后一个订单编码流水号
     *
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($maxModel = self::where('code', 'like', self::codePrefix() . '%')->orderByDesc('code')->first()) {
            return substr($maxModel['code'], strlen(self::codePrefix()));
        }
        return 0;
    }

    /**
     * 订单编码前缀
     *
     * @return string
     */

    protected static function codePrefix()
    {
        return 'PW' . Carbon::now()->format('Ymd');
    }

    /**
     * 生成订单流水号编码
     *
     * @return null|string
     */

    public static function generateCode()
    {
        return CodeGenerator::getUniqueCode(self::class . '-' . self::codePrefix(), function () {
            return self::maxCode();
        }, 3, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
    }

    /**
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_BUSINESS_APPROVAL => '业务审核中',
            self::STATUS_FINANCIAL_APPROVAL => '财务审核中',
            self::STATUS_FINANCIAL_APPROVAL_FAILURE => '财务审核失败',
            self::STATUS_APPROVAL_FAILURE => '审核失败',
            self::STATUS_APPROVAL_SUCCESS => '审核成功'
        ];
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return self::getStatusLabel($this->status);
    }
}
