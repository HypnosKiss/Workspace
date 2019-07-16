<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\Article
 *
 * @property int $id
 * @property string $code 文章编码
 * @property string|null $category 文章分类
 * @property string $title 文章标题
 * @property string $content 文章内容
 * @property int $viewers 查看次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereViewers($value)
 * @mixin \Eloquent
 * @property-read array $content_url
 */
class Article extends Model
{
    protected $casts = [
        'content' => 'array'
    ];

    protected $appends = [
        'content_url'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 5, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'ART';
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

    public function setContentAttribute($values)
    {
        $this->attributes['content'] = collect($values)->map(function ($image) {
            if (empty($image['image'])) $image['imageUrl'] = '';
            return $image;
        });
        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function getContentAttribute()
    {
        return collect(json_decode($this->attributes['content'], true))->map(function ($image) {
            if (!empty($image['image'])) $image['imageUrl'] = upload_file_to_url($image['image']);
            return $image;
        });
    }

    /**
     * @return array
     */

    public function getContentUrlAttribute()
    {
        return collect($this->content)->filter(function ($image) {
            return !empty($image['image']);
        })->pluck('imageUrl');
    }
}
