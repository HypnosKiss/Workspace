<?php

namespace App\Models;

use App\ModelEvents\Tags\DeleteTag;
use App\ModelEvents\Tags\DisableTag;
use App\ModelEvents\Tags\EnableTag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;
use ZhiEq\Utils\Trigger;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $code 标签编码
 * @property int $type 标签类型
 * @property string $name 标签名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $status 状态
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereUpdatedAt($value)
 * @method static array getTypeLabels()
 * @method static array getTypeList()
 * @method static string getTypeLabel($key)
 * @mixin \Eloquent
 * @property-read string $status_text
 * @property-read string $type_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag disabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag onlyPartner()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag onlyProduct()
 */
class Tag extends Model
{
    use DefinitionAttribute;

    const TYPE_PRODUCT = 10;
    const TYPE_PARTNER = 20;

    protected static function typeDefinition()
    {
        return [
            self::TYPE_PRODUCT => '商品',
            self::TYPE_PARTNER => '合伙人'
        ];
    }

    protected $appends = [
        'status_text', 'type_text'
    ];

    /**
     * 引导函数
     */

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
            }, 3, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix());
        });
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'TAG';
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

    public function scopeOnlyProduct(Builder $query)
    {
        return $query->where('type', self::TYPE_PRODUCT);
    }

    /**
     * @param Builder $query
     * @return Builder
     */

    public function scopeOnlyPartner(Builder $query)
    {
        return $query->where('type', self::TYPE_PARTNER);
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
        return Trigger::eventWithTransaction(new DeleteTag($this));
    }

    /**
     * @return bool
     */

    public function toDisabled()
    {
        return Trigger::eventWithTransaction(new DisableTag($this));
    }

    /**
     * @return bool
     */

    public function toEnabled()
    {
        return Trigger::eventWithTransaction(new EnableTag($this));
    }
}
