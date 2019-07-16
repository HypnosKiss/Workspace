<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;
use ZhiEq\Exceptions\CustomException;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;


/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $code 编码
 * @property string $create_user_code 用户编码
 * @property int $type 类型
 * @property int|null $pay_type 支付方式
 * @property int $distribution 配送方式
 * @property string $client_name 收货人名称
 * @property string $client_phone 收货人电话
 * @property string|null $province_code 省编码
 * @property string $province_text 省中文
 * @property string|null $city_code 市编码
 * @property string $city_text 市中文
 * @property string|null $county_code 县编码
 * @property string $county_text 县中文
 * @property string $address 详细地址
 * @property string|null $postal_code 邮政编码
 * @property string|null $user_coupon_code 用户优惠券编码
 * @property int $freight 运费
 * @property int $status 状态
 * @property float $product_total_price 商品总金额
 * @property float|null $discounted_price 优惠金额
 * @property string|null $receive_at 收货时间
 * @property string|null $pay_number 支付订单号
 * @property string|null $pay_price 支付金额
 * @property string|null $pay_at 支付时间
 * @property string|null $remarks 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read float $actually_pay_price
 * @property-read mixed $distribution_text
 * @property-read string $full_address
 * @property-read mixed $invoices
 * @property-read mixed $products
 * @property-read mixed $status_text
 * @property-read \App\Models\OrderInvoice $orderInvoice
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderProduct[] $orderProduct
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCityText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCountyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCountyText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreateUserCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDiscountedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDistribution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereFreight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereProductTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereProvinceText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereReceiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserCouponCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order withoutTrashed()
 * @mixin \Eloquent
 * @method static getDistributionList()
 * @method static getDistributionLabels()
 * @method static getDistributionLabel($key)
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @method static getPayTypeList()
 * @method static getPayTypeLabels()
 * @method static getPayTypeLabel($key)
 * @property-read mixed $create_user_name
 * @property-read mixed $create_user_phone
 * @property-read \App\Models\User $user
 * @property-read string $product_num_text
 * @property-read string $product_text
 * @property string|null $tracking_number 快递单号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTrackingNumber($value)
 * @property-read bool $is_refund
 * @property-read int $is_invoices
 * @property-read string $is_invoices_text
 * @property string|null $tracking_type 快递类型
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTrackingType($value)
 * @property string|null $freight_at 发货时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereFreightAt($value)
 * @property-read mixed $create_user_username
 * @property-read mixed $pay_type_text
 * @property-read mixed $product_total_number
 * @property int $is_evaluation 是否已评价
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereIsEvaluation($value)
 */
class Order extends Model
{
    use DefinitionAttribute, SoftDeletes;

    const TYPE_ORDINARY = 10;  //普通订单
    const TYPE_SPIKE = 20;   //秒杀订单
    const TYPE_FIGHT_GROUP = 30;   //拼团订单
    const TYPE_BARGAIN = 40;  //砍价订单

    const DISTRIBUTION_FREE = 10;  //包邮
    const DISTRIBUTION_EXPRESS_DELIVERY = 20;   //快递

    const STATUS_UNPAID = 5;  //未支付
    const STATUS_PAYMENT_FAILED = 10;   //支付失败
    const STATUS_ABNORMAL = 15;   //支付异常
    const STATUS_CLOSE = 20;  //已关闭
    const STATUS_NOT_SHIPPED = 30;  //未发货
    const STATUS_SHIPPED = 40;  //已发货
    const STATUS_UNRECEIVED = 50;  //未收货
    const STATUS_RECEIVED = 60;  //已收货

    const PAY_TYPE_WE_CHAT = 10;   //微信
    const PAY_TYPE_TRANSFER = 20;  //转账

    protected $appends = [
        'status_text', 'distribution_text'
    ];

    protected $hidden = [
        'orderInvoice', 'orderProduct', 'province_code', 'city_code', 'county_code', 'user'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->create_user_code = auth_user()->code;
            $model->code = self::generateCode();
            $model->status = self::STATUS_UNPAID;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function orderInvoice()
    {
        return $this->hasOne('App\Models\OrderInvoice', 'order_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function orderProduct()
    {
        return $this->hasMany('App\Models\OrderProduct', 'order_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function user()
    {
        return $this->hasOne('App\Models\User', 'code', 'create_user_code');
    }

    /**
     * @return array
     */

    protected static function distributionDefinition()
    {
        return [
            self::DISTRIBUTION_FREE => '包邮',
            self::DISTRIBUTION_EXPRESS_DELIVERY => '快递'
        ];
    }

    /**
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_UNPAID => '未支付',
            self::STATUS_ABNORMAL => '支付失败',
            self::STATUS_ABNORMAL => '支付异常',
            self::STATUS_CLOSE => '已关闭',
            self::STATUS_NOT_SHIPPED => '未发货',
            self::STATUS_SHIPPED => '已发货',
            self::STATUS_UNRECEIVED => '未收货',
            self::STATUS_RECEIVED => '已收货'
        ];
    }

    /**
     * @return array
     */

    protected static function payTypeDefinition()
    {
        return [
            self::PAY_TYPE_WE_CHAT => '微信支付',
            self::PAY_TYPE_TRANSFER => '转账'
        ];
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public static function statusGroup($key)
    {
        $group = [
            10 => [   //待支付
                self::STATUS_UNPAID, self::STATUS_PAYMENT_FAILED
            ],
            20 => [  //待发货
                self::STATUS_NOT_SHIPPED
            ],
            30 => [  //待收货
                self::STATUS_SHIPPED, self::STATUS_UNRECEIVED
            ],
            40 => [  //已完成
                self::STATUS_RECEIVED
            ]
        ];
        return empty($group[$key]) ? null : $group[$key];
    }

    /**
     * 最后一个订单编码流水号
     *
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($maxModel = self::withTrashed()->where('code', 'like', self::codePrefix() . '%')->orderByDesc('code')->first()) {
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
        return 'KA' . Carbon::now()->format('Ymd');
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
     * @return bool
     * @throws \Exception
     */

    public function WeChatPay()
    {
        Log::info('weChat OpenId', ['cacheOpenId' => WxAppletsSession::getOpenid(), 'userOpenId' => auth_user()->open_id]);
        $result = app('wechat.payment')->order->unify([
            'body' => '康佳优品',
            'out_trade_no' => $this->code,
            'total_fee' => $this->actually_pay_price * 100,
            'trade_type' => 'JSAPI',
            'openid' => auth_user()->open_id,
            'notify_url' => env('WX_NOTIFY_URL') . '/order-pay'
        ]);
        Log::info('create weChat order', collect($result)->toArray());
        if ($result['return_code'] !== 'SUCCESS' || $result['result_code'] !== 'SUCCESS') {
            throw new CustomException('调用微信支付失败');
        }
        Log::info('create weChat order success');
        return app('wechat.payment')->jssdk->bridgeConfig($result['prepay_id'], false);
    }

    /**
     * 状态中文
     *
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return self::getStatusLabel($this->status);
    }

    /**
     * 配送方式中文
     *
     * @return mixed
     */

    public function getDistributionTextAttribute()
    {
        return self::getDistributionLabel($this->distribution);
    }

    /**
     * @return mixed
     */

    public function getProductsAttribute()
    {
        return $this->orderProduct->map(function (OrderProduct $item) {

            return [
                'id' => $item->id,
                'title' => $item->name,
                'image_url' => $item->image_url,
                'price' => $item->price,
                'num' => $item->num,
                'product_code' => $item->product_code,
                //'specifications' => $item->specifications,
                'product_specifications_code' => $item->product_specifications_code,
                'specifications_text' => $item->specifications_text
            ];
        });
    }

    public function getCreateUserUsernameAttribute()
    {
        return $this->user->nickname;
    }

    /**
     * 支付金额
     *
     * @return float
     */

    public function getActuallyPayPriceAttribute()
    {
        return round($this->product_total_price + $this->freight - $this->discounted_price, 2);
    }

    /**
     * 全地址
     *
     * @return string
     */

    public function getFullAddressAttribute()
    {
        return $this->province_text . $this->city_text . $this->county_text . $this->address;
    }

    /**
     * @return mixed
     */

    public function getPayTypeTextAttribute()
    {
        return self::getPayTypeLabel($this->pay_type);
    }

    /**
     * 订单发票
     *
     * @return mixed
     */

    public function getInvoicesAttribute()
    {
        return $this->orderInvoice;
    }

    /**
     * 创建人名称
     *
     * @return mixed
     */

    public function getCreateUserNameAttribute()
    {
        return $this->user->nickname;
    }

    /**
     * 创建人电话
     *
     * @return mixed
     */

    public function getCreateUserPhoneAttribute()
    {
        return $this->user->phone;
    }

    /**
     * @return string
     */

    public function getProductTextAttribute()
    {
        return $this->orderProduct->pluck('name')->implode(';');
    }

    /**
     * @return string
     */

    public function getProductNumTextAttribute()
    {
        return $this->orderProduct->pluck('num')->implode(';');
    }

    /**
     * @return float
     */

    public function getProductTotalPriceAttribute()
    {
        return round($this->orderProduct->map(function ($item) {
            return $item['price'] * $item['num'];
        })->sum(), 2);
    }

    /**
     * @return mixed
     */

    public function getProductTotalNumberAttribute()
    {
        return $this->orderProduct->sum('num');
    }

    /**
     * @return int
     */

    public function getIsRefundAttribute()
    {
        if (RefundOrder::whereOrderCode($this->code)->where('status', '!=', RefundOrder::STATUS_FINISH)->exists()) {
            return PublicDefinition::SWITCH_YES;
        }
        return PublicDefinition::SWITCH_NO;
    }

    /**
     * @return int
     */

    public function getIsInvoicesAttribute()
    {
        if (empty($this->orderInvoice)) {
            return 20;
        }
        return 10;
    }

    /**
     * @return string
     */

    public function getIsInvoicesTextAttribute()
    {
        return $this->is_invoices === 10 ? '是' : '否';
    }
}
