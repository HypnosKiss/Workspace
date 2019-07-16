<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;


/**
 * App\Models\RefundOrder
 *
 * @property int $id
 * @property string $code 编码
 * @property string $user_code 用户编码
 * @property int $type 类型
 * @property string $order_code 订单编码
 * @property int $refund_type 退货方式
 * @property string $content 问题描述
 * @property array|null $images 问题图片
 * @property float|null $price 退货金额
 * @property int $status 状态
 * @property string|null $tracking_type 快递类型
 * @property string|null $tracking_number 快递单号
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $product_code 产品编码
 * @property int $num 退货件数
 * @property string|null $refund_transaction_order 退款交易单号
 * @property string $product_title 商品名称
 * @property string $product_sub_title 商品副标题
 * @property string $product_image 商品图片
 * @property float $product_price 商品售价
 * @property-read mixed $refund_type_text
 * @property-read mixed $status_text
 * @property-read mixed $type_text
 * @property-read \App\Models\Order $order
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RefundOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereOrderCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereProductImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereProductSubTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereProductTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereRefundTransactionOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereTrackingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereUserCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RefundOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RefundOrder withoutTrashed()
 * @method static array getTypeLabels()
 * @method static array getTypeList()
 * @method static string getTypeLabel($key)
 * @method static array getRefundTypeLabels()
 * @method static array getRefundTypeList()
 * @method static string getRefundTypeLabel($key)
 * @method static array getStatusLabels()
 * @method static array getStatusList()
 * @method static string getStatusLabel($key)
 * @mixin \Eloquent
 * @property string|null $fail_reason 拒绝审核原因
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereFailReason($value)
 * @property string|null $product_specification_text
 * @property-read string|null $product_image_url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundOrder whereProductSpecificationText($value)
 */
class RefundOrder extends Model
{
    use DefinitionAttribute, SoftDeletes;

    const TYPE_RETREAT = 10;   //退
    const TYPE_CHANGE = 20;  //换

    const REFUND_TYPE_VISIT = 10;   //上门
    const REFUND_TYPE_EXPRESS_DELIVERY = 20;   //快递

    const STATUS_REFUSE = 5;  //拒绝申请
    const STATUS_WAIT_AUDIT = 10;   //待审核
    const STATUS_PASS_REFUND = 30;  //待退款
    const STATUS_PASS_EXCHANGE = 40;   //待配送
    const STATUS_EXCHANGE_SEND = 50;   //配送中
    const STATUS_FINISH = 99;//已完成

    protected $casts = [
        'images' => 'array'
    ];

    protected $appends = [
        'type_text', 'refund_type_text', 'status_text', 'product_image_url'
    ];

    protected $hidden = [
        'order'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->status = self::STATUS_WAIT_AUDIT;
            $model->code = self::generateCode();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function order()
    {
        return $this->hasOne('App\Models\Order', 'code', 'order_code');
    }

    /**
     * @return array
     */

    protected static function typeDefinition()
    {
        return [
            self::TYPE_RETREAT => '退货',
            self::TYPE_CHANGE => '换货'
        ];
    }

    /**
     * @return array
     */

    protected static function RefundTypeDefinition()
    {
        return [
            self::REFUND_TYPE_VISIT => '上门取件',
            self::REFUND_TYPE_EXPRESS_DELIVERY => '快递'
        ];
    }

    /**
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_REFUSE => '拒绝申请',
            self::STATUS_WAIT_AUDIT => '待审核',
            self::STATUS_PASS_REFUND => '待退款',
            self::STATUS_PASS_EXCHANGE => '待配送',
            self::STATUS_EXCHANGE_SEND => '配送中',
            self::STATUS_FINISH => '已完成'
        ];
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
        return 'RKA' . Carbon::now()->format('Ymd');
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
        }, 5, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
    }

    /**
     * @return string|null
     */

    public function getProductImageUrlAttribute()
    {
        return upload_file_to_url($this->product_image);
    }

    /**
     * @return mixed
     */

    public function getTypeTextAttribute()
    {
        return self::getTypeLabel($this->type);
    }

    /**
     * @return mixed
     */

    public function getRefundTypeTextAttribute()
    {
        return self::getRefundTypeLabel($this->refund_type);
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return self::getStatusLabel($this->status);
    }
}
