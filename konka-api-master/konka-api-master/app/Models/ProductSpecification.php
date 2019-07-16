<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use ZhiEq\Utils\CodeGenerator;
use Carbon\Carbon;

/**
 * App\Models\ProductSpecification
 *
 * @property int $id
 * @property string $code 编码
 * @property array $specification_codes 规格编码组
 * @property string $product_code 产品编码
 * @property float $price 价格
 * @property float|null $discount_price 优惠价格
 * @property float|null $distribution_price 分销价格
 * @property float|null $distribution_num 分销点数
 * @property int $stock 库存
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $image 规格组合图片
 * @property string|null $combination_code 组合编码
 * @property float $guide_price 指导价
 * @property-read int $bargain_price
 * @property-read mixed $image_url
 * @property-read mixed $specification_codes_text
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereCombinationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereDiscountPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereDistributionNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereDistributionPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereGuidePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereSpecificationCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSpecification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductSpecification extends Model
{
    protected $casts = [
        'specification_codes' => 'array'
    ];

    protected $attributes = [
        'distribution_num' => 0
    ];

    protected $hidden = [
        'productImage', 'product'
    ];

    protected $appends = [
        'image_url'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->price = 0;
            $model->stock = 0;
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 10, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'code', 'product_code');
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'PS';
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
     * @return mixed
     */

    public function getImageUrlAttribute()
    {
        return upload_file_to_url(!empty($this->image) ? $this->image : $this->product->main_image);
    }

    /**
     * @return mixed
     */

    public function getSpecificationCodesTextAttribute()
    {
        $subSpe = collect($this->product->specification_array)->pluck('sub_specification')->collapse()
            ->whereIn('code', $this->specification_codes);
        return collect($subSpe)->pluck('name')->implode(';');
    }
}
