<?php

namespace App\Models;

use App\ModelEvents\CommissionRules\DeleteRule;
use App\ModelEvents\CommissionRules\DisabledRule;
use App\ModelEvents\CommissionRules\EnabledRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;
use ZhiEq\Utils\Trigger;

/**
 * App\Models\CommissionRule
 *
 * @property int $id
 * @property string $code 规则编码
 * @property string $name 规则名称
 * @property int $type 规则类型
 * @property string $begin_time 开始时间
 * @property string $end_time 结束时间
 * @property float $first_level_commission_percentage 一级分佣比例
 * @property float $second_level_commission_percentage 二级分佣比例
 * @property int $order 优先级
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereBeginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereFirstLevelCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereSecondLevelCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule whereUpdatedAt($value)
 * @method static array getTypeLabels()
 * @method static array getTypeList()
 * @method static string getTypeLabel($key)
 * @mixin \Eloquent
 * @property-read string $status_text
 * @property-read string $type_text
 * @property-write array $partners
 * @property-write array $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule disabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule enabled()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CommissionRulePartnerRelation[] $partnerTags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CommissionRuleProductRelation[] $productTags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRule effective()
 */
class CommissionRule extends Model
{
    use DefinitionAttribute;

    const TYPE_TAGS = 10;

    protected static function typeDefinition()
    {
        return [
            self::TYPE_TAGS => '标签'
        ];
    }

    protected $attributes = [
        'type' => self::TYPE_TAGS
    ];

    protected $appends = [
        'products', 'partners', 'status_text'
    ];

    protected $_partners = [];

    protected $_products = [];

    protected static function boot()
    {
        parent::boot();
        self::saving(function (self $model) {
            if (!$model->exists) {
                $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                    return self::maxCode();
                }, 4, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
            }
            if (!$model->writeProductTags()) {
                return false;
            }
            if (!$model->writePartnerTags()) {
                return false;
            }
        });
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'CR';
    }

    /**
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($model = self::orderByDesc('code')->first()) {
            return substr($model->code, strlen(self::codePrefix()));
        }
        return 0;
    }

    /**
     * @return bool
     */

    protected function writeProductTags()
    {
        if (empty($this->_products)) {
            return true;
        }
        $needCreateTags = array_diff($this->_products, $this->products);
        $needDeleteTags = array_diff($this->products, $this->_products);
        if (count($needCreateTags) !== count(array_filter($needCreateTags, function ($tagCode) {
                return (new CommissionRuleProductRelation())
                    ->setAttribute('commission_rule_code', $this->code)
                    ->setAttribute('product_tag_code', $tagCode)
                    ->save();
            }))) {
            return false;
        }
        if (count($needDeleteTags) !== count(array_filter($needDeleteTags, function ($tagCode) {
                return CommissionRuleProductRelation::whereProductTagCode($tagCode)
                    ->whereCommissionRuleCode($this->code)
                    ->delete();
            }))) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */

    protected function writePartnerTags()
    {
        if (empty($this->_partners)) {
            return true;
        }
        $needCreateTags = array_diff($this->_partners, $this->partners);
        $needDeleteTags = array_diff($this->partners, $this->_partners);
        if (count($needCreateTags) !== count(array_filter($needCreateTags, function ($tagCode) {
                return (new CommissionRulePartnerRelation())
                    ->setAttribute('commission_rule_code', $this->code)
                    ->setAttribute('partner_tag_code', $tagCode)
                    ->save();
            }))) {
            return false;
        }
        if (count($needDeleteTags) !== count(array_filter($needDeleteTags, function ($tagCode) {
                return CommissionRulePartnerRelation::wherePartnerTagCode($tagCode)
                    ->whereCommissionRuleCode($this->code)
                    ->delete();
            }))) {
            return false;
        }
        return true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function productTags()
    {
        return $this->hasMany(CommissionRuleProductRelation::class, 'commission_rule_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function partnerTags()
    {
        return $this->hasMany(CommissionRulePartnerRelation::class, 'commission_rule_code', 'code');
    }

    /**
     * @param Builder $query
     * @return Builder
     */

    public function scopeEnabled(Builder $query)
    {
        return $query->where('status', PublicDefinition::STATUS_ENABLED);
    }

    /**
     * @param Builder $query
     * @return Builder
     */

    public function scopeDisabled(Builder $query)
    {
        return $query->where('status', PublicDefinition::STATUS_DISABLED);
    }

    /**
     * @param Builder $query
     * @return Builder
     */

    public function scopeEffective(Builder $query)
    {
        return $query->where('begin_time', '<=', Carbon::now())->where('end_time', '>=', Carbon::now());
    }

    /**
     * @param $value
     */

    public function setProductsAttribute($value)
    {
        $this->_products = $value;
    }

    /**
     * @param $value
     */

    public function setPartnersAttribute($value)
    {
        $this->_partners = $value;
    }

    /**
     * @return array
     */

    public function getProductsAttribute()
    {
        return $this->productTags->pluck('product_tag_code')->toArray();
    }

    /**
     * @return array
     */

    public function getPartnersAttribute()
    {
        return $this->partnerTags->pluck('partner_tag_code')->toArray();
    }

    /**
     * @return string
     */

    public function getBeginTimeAttribute()
    {
        return (new Carbon($this->attributes['begin_time']))->format('Y-m-d H:i');
    }

    /**
     * @return string
     */

    public function getEndTimeAttribute()
    {
        return (new Carbon($this->attributes['end_time']))->format('Y-m-d H:i');
    }

    /**
     * @return string
     */

    public function getStatusTextAttribute()
    {
        return PublicDefinition::getStatusLabel($this->status);
    }

    /**
     * @return string
     */

    public function getTypeTextAttribute()
    {
        return self::getTypeLabel($this->type);
    }

    /**
     * @return bool
     */

    public function toDelete()
    {
        return Trigger::eventWithTransaction(new DeleteRule($this));
    }

    /**
     * @return bool
     */

    public function toDisabled()
    {
        return Trigger::eventWithTransaction(new DisabledRule($this));
    }

    /**
     * @return bool
     */

    public function toEnabled()
    {
        return Trigger::eventWithTransaction(new EnabledRule($this));
    }

    /**
     * @param $partnerCode
     * @param $productCode
     * @return array
     */

    public static function getCommissionProportion($partnerCode, $productCode)
    {
        $rules = self::effective()->enabled()->orderByDesc('order')->orderByDesc('created_at')->get();
        $partnerTags = PartnerTag::wherePartnerCode($partnerCode)->get()->pluck('tag_code')->toArray();
        $productTags = ProductTag::whereProductCode($productCode)->get()->pluck('tag_code')->toArray();
        $activeRule = null;
        $rules->each(function ($rule) use (&$activeRule, $partnerTags, $productTags) {
            if (count(array_intersect($partnerTags, $rule->partners)) > 0 && count(array_intersect($productTags, $rule->products)) > 0) {
                $activeRule = $rule;
                return false;
            }
        });
        $firstPercentage = empty($activeRule) ? Setting::getValue(Setting::SETTING_KEY_PARTNER_FIRST_PERCENTAGE) : $activeRule['first_level_commission_percentage'];
        $secondPercentage = empty($activeRule) ? Setting::getValue(Setting::SETTING_KEY_PARTNER_SECOND_PERCENTAGE) : $activeRule['second_level_commission_percentage'];
        return [
            'firstPercentage' => $firstPercentage,
            'secondPercentage' => $secondPercentage
        ];
    }
}
