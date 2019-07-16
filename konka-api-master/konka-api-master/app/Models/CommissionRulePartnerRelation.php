<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\CommissionRulePartnerRelation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $code
 * @property string $commission_rule_code 合伙人规则
 * @property string $partner_tag_code 合伙人标签编号
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation whereCommissionRuleCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation wherePartnerTagCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommissionRulePartnerRelation whereUpdatedAt($value)
 * @property-read string $status_text
 * @property-read string $tag_name
 * @property-read \App\Models\Tag $tag
 */
class CommissionRulePartnerRelation extends Model
{
    protected $table = 'commission_rule_partner_relation';

    protected $attributes = [
        'status' => PublicDefinition::STATUS_ENABLED
    ];

    protected $appends = [
        'status_text'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 6, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'CRPR';
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'product_tag_code', 'code');
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

    public function getTagNameAttribute()
    {
        return $this->tag->name;
    }


    /**
     * @return mixed
     */

    public function toDisabled()
    {
        return $this->setAttribute('status', PublicDefinition::STATUS_DISABLED)->save();
    }

    /**
     * @return mixed
     */

    public function toEnabled()
    {
        return $this->setAttribute('status', PublicDefinition::STATUS_ENABLED)->save();
    }
}
