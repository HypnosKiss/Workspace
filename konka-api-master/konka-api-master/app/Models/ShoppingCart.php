<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ShoppingCart
 *
 * @property int $id
 * @property string $user_code 用户编码
 * @property string $product_code 产品编码
 * @property string $product_specification_code 产品规格编码
 * @property int $num 数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart whereProductSpecificationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShoppingCart whereUserCode($value)
 * @mixin \Eloquent
 * @property-read mixed $name
 * @property-read \App\Models\ProductSpecification $productSpecification
 * @property-read mixed $image_url
 * @property-read float $price
 * @property-read mixed $product_specification_text
 * @property-read \App\Models\Product $product
 */
class ShoppingCart extends Model
{
    protected $hidden = [
        'productSpecification'
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function productSpecification()
    {
        return $this->hasOne('App\Models\ProductSpecification', 'code', 'product_specification_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }

    /**
     * @return string
     */

    public function getNameAttribute()
    {
        return $this->product->title;
    }

    /**
     * @return float
     */

    public function getPriceAttribute()
    {
        return $this->productSpecification->price;
    }

    /**
     * @return mixed
     */

    public function getImageUrlAttribute()
    {
        return $this->productSpecification->image_url;
    }

    /**
     * @return mixed
     */

    public function getProductSpecificationTextAttribute()
    {
        return $this->productSpecification->specification_codes_text;
    }
}
