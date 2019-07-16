<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductImage
 *
 * @property int $id
 * @property string $product_code 产品编码
 * @property string $product_specification_code 产品规格编码
 * @property string $image 图片
 * @property int $order 排序
 * @property int $type 类型
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereProductSpecificationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $image_url
 */
class ProductImage extends Model
{
    const TYPE_IMAGE = 10;
    const TYPE_CONTENT = 20;
    const TYPE_SPECIFICATION = 30;
    const TYPE_THUMB = 40;

    protected $appends = [
        'image_url'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }
        return upload_file_to_url($this->image);
    }
}
