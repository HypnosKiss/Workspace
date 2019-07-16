<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\Specification
 *
 * @property int $id
 * @property string $code 编码
 * @property string $parent_code 上级编码
 * @property string $name 名称
 * @property int $order 排序
 * @property int|null $level 级别
 * @property int $status 状态
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $status_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereParentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Specification[] $subSpecification
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification disable()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Specification enable()
 * @property-read \Illuminate\Database\Eloquent\Collection $sub_enable_specification
 * @property-read mixed $parent_name
 * @property-read \App\Models\Specification|null $parentModel
 */
class Specification extends Model
{
    use DefinitionAttribute;

    protected $appends = [
        'status_text','parent_name'
    ];

    const STATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->status = self::STATUS_ENABLE;
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 6, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function subSpecification()
    {
        return $this->hasMany(self::class, 'parent_code', 'code')->orderBy('order', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function parentModel()
    {
        return $this->belongsTo(self::class, 'parent_code', 'code');
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'S';
    }

    /**
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
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_ENABLE => '启用',
            self::STATUS_DISABLE => '禁用'
        ];
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return self::getStatusLabel($this->status);
    }

    /**
     *
     */

    public function getParentNameAttribute()
    {
        if (empty($this->parent_code)) {
            return '无';
        }
        return $this->parentModel->name;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getSubEnableSpecificationAttribute()
    {
        return $this->subSpecification->where('status', self::STATUS_ENABLE);
    }

    /**
     * @param Builder $query
     * @return mixed
     */

    public function scopeEnable($query)
    {
        return $query->where('status', self::STATUS_ENABLE);
    }

    /**
     * @param Builder $query
     * @return mixed
     */

    public function scopeDisable($query)
    {
        return $query->where('status', self::STATUS_DISABLE);
    }
}
