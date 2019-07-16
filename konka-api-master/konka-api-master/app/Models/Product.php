<?php

namespace App\Models;


use App\Extend\PosterBuildService;
use App\Extend\WeChatService;
use Aws\S3\S3Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;


/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $code 编码
 * @property string|null $konka_product_code 康佳内部系统产品编码
 * @property string $product_category_code 产品分类编码
 * @property string $title 标题
 * @property string|null $sub_title 子标题
 * @property int $order 排序
 * @property int $status 状态
 * @property int $sales 销量
 * @property int $is_hot 是否热卖
 * @property int $is_recommend 是否推荐
 * @property int $is_new 是否新品
 * @property int|null $min 最小购买
 * @property int|null $max 最大购买
 * @property int|null $per 每次购买数
 * @property string|null $start_at 开始时间
 * @property string|null $end_at 结束时间
 * @property array $specification_array 规格组
 * @property string $create_person_code 创建人
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float $price 最小价格
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CouponProduct[] $couponProducts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Evaluation[] $evaluation
 * @property-read \Illuminate\Database\Eloquent\Collection $content
 * @property-read string $create_person_name
 * @property-read int $evaluation_num
 * @property-read \Illuminate\Database\Eloquent\Collection $images
 * @property-read mixed $is_hot_text
 * @property-read mixed $is_new_text
 * @property-read mixed $is_recommend_text
 * @property-read mixed $main_image
 * @property-read mixed $main_image_url
 * @property-read mixed $one_evaluation
 * @property-read string $product_category_name
 * @property-read \Illuminate\Support\Collection $product_coupon
 * @property-read \Illuminate\Support\Collection $product_specifications
 * @property-read mixed $status_text
 * @property-read \App\Models\ProductCategory $productCategory
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductImage[] $productImage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductSpecification[] $productSpecification
 * @property-read \App\Models\Admin $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product disable()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product enable()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCreatePersonCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereIsNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereIsRecommend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereKonkaProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereProductCategoryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSpecificationArray($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSubTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Product withoutTrashed()
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @method static getIsHotList()
 * @method static getIsHotLabels()
 * @method static getIsHotLabel($key)
 * @method static getIsRecommendList()
 * @method static getIsRecommendLabels()
 * @method static getIsRecommendLabel($key)
 * @method static getIsNewList()
 * @method static getIsNewLabels()
 * @method static getIsNewLabel($key)
 * @mixin \Eloquent
 * @property-read array $specification
 * @property-read \Illuminate\Database\Eloquent\Collection $image
 * @property-read int $commission_amount
 * @property-read string $share_code_url
 * @property-read string $share_poster_url
 * @property-read mixed $guide_price
 */
class Product extends Model
{
    use DefinitionAttribute, SoftDeletes;

    const IS_HOT_YES = 10;
    const IS_HOT_NO = 20;
    const IS_RECOMMEND_YES = 10;
    const IS_RECOMMEND_NO = 20;
    const IS_NEW_YES = 10;
    const IS_NEW_NO = 20;

    protected $casts = [
        'specification_array' => 'array'
    ];

    protected $hidden = [
        'productCategory', 'productImage', 'productSpecification', 'couponProducts', 'evaluation', 'user'
    ];

    const STATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->create_person_code = auth_admin()->code;
            $model->status = self::STATUS_DISABLE;
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 10, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * 关联产品分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function productCategory()
    {
        return $this->hasOne('App\Models\ProductCategory', 'code', 'product_category_code');
    }

    /**
     * 关联产品图片
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function productImage()
    {
        return $this->hasMany('App\Models\ProductImage', 'product_code', 'code')->orderBy('order');
    }

    /**
     * 关联产品规格
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function productSpecification()
    {
        return $this->hasMany('App\Models\ProductSpecification', 'product_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function evaluation()
    {
        return $this->hasMany('App\Models\Evaluation', 'product_code', 'code')->orderBy('id', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function user()
    {
        return $this->hasOne('App\Models\Admin', 'code', 'create_person_code');
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'P';
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
            self::STATUS_ENABLE => '上架',
            self::STATUS_DISABLE => '下架'
        ];
    }

    /**
     * @return array
     */

    protected static function isHotDefinition()
    {
        return [
            self::IS_HOT_YES => '是',
            self::IS_HOT_NO => '否'
        ];
    }

    /**
     * @return array
     */

    protected static function isRecommendDefinition()
    {
        return [
            self::IS_RECOMMEND_YES => '是',
            self::IS_RECOMMEND_NO => '否'
        ];
    }

    /**
     * @return array
     */

    protected static function isNewDefinition()
    {
        return [
            self::IS_NEW_YES => '是',
            self::IS_NEW_NO => '否'
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
     * @return string
     */

    public function getProductCategoryNameAttribute()
    {
        if (empty($this->productCategory)) {
            return '无';
        }
        return $this->productCategory->name;
    }

    /**
     * @return mixed
     */

    public function getIsHotTextAttribute()
    {
        return self::getIsHotLabel($this->is_hot);
    }

    /**
     * @return mixed
     */

    public function getIsRecommendTextAttribute()
    {
        return self::getIsRecommendLabel($this->is_recommend);
    }

    /**
     * @return mixed
     */

    public function getIsNewTextAttribute()
    {
        return self::getIsNewLabel($this->is_new);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getImagesAttribute()
    {
        return $this->productImage->where('type', ProductImage::TYPE_IMAGE)->values();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getImageAttribute()
    {
        return $this->images;
    }

    /**
     * @return mixed
     */

    public function getMainImageAttribute()
    {
        if ($image = $this->productImage->where('type', ProductImage::TYPE_THUMB)->first()) {
            return $image->image;
        }
        return $this->images->first()['image'];
    }

    /**
     * @return array
     */

    public function getSpecificationAttribute()
    {
        $specificationList = [];
        foreach ($this->specification_array as $category) {
            foreach ($category['sub_specification'] as $specification) {
                $specificationList[] = $specification['code'];
            }
        }
        return $specificationList;
    }

    /**
     * @return mixed
     */

    public function getMainImageUrlAttribute()
    {
        if ($image = $this->productImage->where('type', ProductImage::TYPE_THUMB)->first()) {
            return $image['imageUrl'];
        }
        return $this->images->first()['imageUrl'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getContentAttribute()
    {
        return $this->productImage->where('type', ProductImage::TYPE_CONTENT)->values();
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function getProductSpecificationsAttribute()
    {
        return $this->productSpecification;
    }

    /**
     * @return mixed
     */

    public function getOneEvaluationAttribute()
    {
        if ($eva = $this->evaluation()->orderByDesc('created_at')->first()) {
            return [$eva];
        }
        return [];
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
     * @return int
     */

    public function getEvaluationNumAttribute()
    {
        return $this->evaluation->count();
    }

    /**
     * @return string
     */

    public function getCreatePersonNameAttribute()
    {
        if (empty($this->user)) {
            return '[管理员已删除]';
        }
        return $this->user->username;
    }

    /**
     * @return mixed
     */

    public function getPriceAttribute()
    {
        return round($this->productSpecification->pluck('price')->sort()->first(), 2);
    }

    /**
     * @return mixed
     */

    public function getGuidePriceAttribute()
    {
        return round($this->productSpecification->pluck('guide_price')->sort()->first(), 2);
    }

    /**
     * @return int
     */

    public function getCommissionAmountAttribute()
    {
        logs()->info('product commission amount', ['user' => auth_user()]);
        if (empty(auth_user())) {
            logs()->info('product commission not user');
            return 0;
        } elseif (empty(auth_user()->partner)) {
            logs()->info('product commission not partner');
            return 0;
        } else {
            $proportions = CommissionRule::getCommissionProportion(auth_user()->partner->code, $this->code);
            logs()->info('product commission proportions', ['proportions' => $proportions]);
            return round($this->price * ($proportions['firstPercentage'] / 100), 2);
        }
    }

    /**
     * @return string
     */

    public function getShareCodeUrlAttribute()
    {
        if (empty(auth_user())) {
            return '';
        } elseif (empty(auth_user()->partner)) {
            return '';
        } else {
            return WeChatService::generateCode('U:' . auth_user()->partner->code . ',P:' . $this->code, 'pages/product-detail/main')['fileUrl'];
        }
    }

    /**
     * @return string
     */

    public function getSharePosterUrlAttribute()
    {
        return '';
    }
}
