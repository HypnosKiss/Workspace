<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderProduct
 *
 * @property int $id
 * @property string $order_code 订单编码
 * @property string $product_code 产品编码
 * @property string $name 产品名称
 * @property string $specifications 产品规格组
 * @property float $price 产品价格
 * @property int $num 数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereOrderCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereSpecifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $product_specifications_code 产品规格编码
 * @property string|null $image 产品图片
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereProductSpecificationsCode($value)
 * @property-read null|string $image_url
 * @property string|null $sub_title 子标题
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderProduct whereSubTitle($value)
 * @property-read mixed $specifications_text
 */
class OrderProduct extends Model
{
    protected $casts = [
        'specifications' => 'array'
    ];

    protected $appends = [
        'image_url'
    ];

    /**
     * @return null|string
     */

    public function getImageUrlAttribute()
    {
        return upload_file_to_url($this->image);
    }

    /**
     * @return mixed
     */

    public function getSpecificationsTextAttribute()
    {
        return collect($this->specifications)->pluck('sub_specification')->collapse()->implode('name', ';');
    }
}