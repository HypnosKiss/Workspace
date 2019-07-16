<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\ProductCategory
 *
 * @property int $id
 * @property string $code 编码
 * @property string $parent_code 上级编码
 * @property string $name 名称
 * @property string|null $image 图片
 * @property int|null $level 级别
 * @property int $order 排序
 * @property int $status 状态
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereParentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @method static getRecommendList()
 * @method static getRecommendLabels()
 * @method static getRecommendLabel($key)
 * @property-read mixed $status_text
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductCategory[] $subCategory
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory disable()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory enable()
 * @property-read null|string $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection $sub_enable_category
 * @property int $recommend 分类推荐(首页显示)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereRecommend($value)
 * @property-read mixed $parent_name
 * @property-read \App\Models\ProductCategory $parentModel
 * @property-read mixed $recommend_text
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductCategory whereDeletedAt($value)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProductCategory onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProductCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProductCategory withoutTrashed()
 */
class ProductCategory extends Model
{
    use DefinitionAttribute, SoftDeletes;

    protected $appends = [
        'status_text', 'image_url', 'parent_name', 'recommend_text'
    ];

    const STATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;

    const RECOMMEND_DISABLED = 10;
    const RECOMMEND_ENABLED = 20;

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

    public function subCategory()
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
        return 'C';
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
     * @return array
     */

    protected static function recommendDefinition()
    {
        return [
            self::RECOMMEND_DISABLED => '否',
            self::RECOMMEND_ENABLED => '是'
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
     * @return mixed
     */

    public function getRecommendTextAttribute()
    {
        return self::getRecommendLabel($this->recommend);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getSubEnableCategoryAttribute()
    {
        return $this->subCategory->where('status', self::STATUS_ENABLE);
    }

    /**
     * @return string
     */

    public function getParentNameAttribute()
    {
        if (empty($this->parent_code)) {
            return '无上级';
        }
        if (empty($this->parentModel)) {
            return '无上级';
        }
        return $this->parentModel->name;
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

    /**
     * @return null|string
     */

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }
        return upload_file_to_url($this->image);
    }
}
