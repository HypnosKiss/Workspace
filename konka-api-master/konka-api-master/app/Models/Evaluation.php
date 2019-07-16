<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\Evaluation
 *
 * @property int $id
 * @property string $code 编码
 * @property string $user_code 用户编码
 * @property string $product_code 产品编码
 * @property string $product_specification_array 产品编码
 * @property int $rate 评分
 * @property string|null $content 评价内容
 * @property array|null $images 图片组
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereProductSpecificationArray($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereUserCode($value)
 * @mixin \Eloquent
 * @property string $specification_text 规格
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EvaluationReply[] $evaluationReply
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereSpecificationText($value)
 * @property-read mixed $nickname
 * @property-read \App\Models\User $user
 * @property-read null|string $avatar
 * @property-read string|null $username
 * @property-read \App\Models\Product $product
 * @property-read string $product_name
 * @property int $status 评价状态
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereStatus($value)
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @property-read mixed $status_text
 * @property string|null $order_code 订单编号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluation whereOrderCode($value)
 */
class Evaluation extends Model
{
    use DefinitionAttribute;

    const STATUS_NO_REPLY = 10;
    const STATUS_HAS_REPLY = 20;

    protected $casts = [
        'images' => 'array'
    ];

    protected $appends = [
        'nickname', 'avatar',
        'username', 'product_name',
        'statusText'
    ];

    protected $hidden = [
        'user'
    ];

    protected static function statusDefinition()
    {
        return [
            self::STATUS_NO_REPLY => '未回复',
            self::STATUS_HAS_REPLY => '已回复'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->code = self::generateCode();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function evaluationReply()
    {
        return $this->hasMany('App\Models\EvaluationReply', 'evaluation_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function user()
    {
        return $this->hasOne('App\Models\User', 'code', 'user_code');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
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
        return 'E' . Carbon::now()->format('Ymd');
    }

    /**
     * @param $value
     * @return mixed
     */

    public function getImagesAttribute($value)
    {
        return $this->attributes['images'] = collect(json_decode($value, true))->map(function ($item) {
            $image = $item;
            $image['image_url'] = upload_file_to_url($item['image']);
            return $image;
        })->sortBy('order')->values();
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
     * @return string
     */

    public function getProductNameAttribute()
    {
        return $this->product->title;
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

    public function getNicknameAttribute()
    {
        return empty($this->user->nickname) ? '' : $this->user->nickname;
    }

    /**
     * @return string|null
     */

    public function getUsernameAttribute()
    {
        return $this->user->username;
    }

    /**
     * @return null|string
     */

    public function getAvatarAttribute()
    {
        return $this->user->avatar;
    }
}
