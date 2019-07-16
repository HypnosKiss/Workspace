<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\Advertisement
 *
 * @property int $id
 * @property int $position 位置
 * @property string|null $title 标题
 * @property string|null $content 内容
 * @property string|null $extend 扩展
 * @property int $order 排序
 * @property int $status 状态
 * @property string|null $start_at 开始时间
 * @property string|null $end_at 结束时间
 * @property string|null $connect 跳转地址
 * @property int|null $connect_type 跳转类型
 * @property int|null $type 广告类型
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement disable()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement enable()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereConnect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereConnectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereExtend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @method static getPositionList()
 * @method static getPositionLabels()
 * @method static getPositionLabel($key)
 * @method static getContactTypeList()
 * @method static getContactTypeLabels()
 * @method static getContactTypeLabel($key)
 * @property string|null $create_person_code
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereCreatePersonCode($value)
 * @property string|null $code
 * @property-read mixed $status_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advertisement whereCode($value)
 * @property-read \App\Models\Admin $createPerson
 * @property-read null|string $content_url
 * @property-read mixed $create_person_name
 * @property-read mixed $position_name
 */
class Advertisement extends Model
{
    use DefinitionAttribute;

    const POSITION_INDEX = 10;

    const STATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;

    const CONTACT_TYPE_PRODUCT = 10;

    protected $attributes = [
        'status' => self::STATUS_ENABLE
    ];

    protected $appends = [
        'status_text',
        'content_url'
    ];

    protected $hidden = [
        'createPerson'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->create_person_code = auth_admin()->code;
            $model->code = CodeGenerator::getNext(self::maxCode(), 5, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function createPerson()
    {
        return $this->hasOne('App\Models\Admin', 'code', 'create_person_code');
    }

    /**
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_ENABLE => '上架',
            self::STATUS_DISABLE => '下架'
        ];
    }

    /**
     * @return array
     */

    protected static function positionDefinition()
    {
        return [
            self::POSITION_INDEX => '首页轮播图(750*360)'
        ];
    }

    /**
     * @return array
     */

    protected static function contactTypeDefinition()
    {
        return [
            self::CONTACT_TYPE_PRODUCT => '商品详情'
        ];
    }

    /**
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($max = self::orderByDesc('code')->first()) {
            return substr($max->code, strlen(self::codePrefix()));
        }
        return 0;
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'A';
    }

    /**
     * 状态文字
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return self::getStatusLabel($this->status);
    }

    /**
     * 广告位置名称
     * @return mixed
     */

    public function getPositionNameAttribute()
    {
        return self::getPositionLabel($this->position);
    }

    /**
     * @return mixed
     */

    public function getCreatePersonNameAttribute()
    {
        if (empty($this->createPerson)) {
            return '[管理员已删除]';
        }
        return $this->createPerson->username;
    }

    /**
     * @return null|string
     */

    public function getContentUrlAttribute()
    {
        return upload_file_to_url($this->content);
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
