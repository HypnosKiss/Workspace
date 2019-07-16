<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\PartnerTag
 *
 * @property int $id
 * @property string $code
 * @property string $partner_code 合伙人编号
 * @property string $tag_code 标签编号
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag wherePartnerCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag whereTagCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PartnerTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PartnerTag extends Model
{
    /**
     * @var array
     */

    protected $attributes = [
        'status' => PublicDefinition::STATUS_ENABLED
    ];

    /**
     *
     */

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
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
        return 'PRR';
    }
}
