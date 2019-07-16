<?php

namespace App\Models;


use App\Extend\WeChatService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $code 用户编码
 * @property string|null $username 用户名
 * @property string|null $password 密码
 * @property string|null $phone 电话
 * @property string|null $email 邮箱
 * @property string|null $avatar 头像
 * @property string|null $nickname 昵称
 * @property int|null $sex 性别
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserAddresses[] $addresses
 * @property-read mixed $default_addresses
 * @property-read mixed $default_invoices
 * @property-read bool $is_partner
 * @property-read string $open_id
 * @property-read mixed $partner_status
 * @property-read mixed $status_text
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserInvoice[] $invoices
 * @property-read \App\Models\Partner $partner
 * @property-read \App\Models\UserOauth $userOauth
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 * @mixin \Eloquent
 * @property string|null $partner_code 登录合伙人账号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePartnerCode($value)
 */
class User extends Model implements Authenticatable
{
    use DefinitionAttribute;

    protected $attributes = [
        'status' => self::STATUS_ENABLE
    ];

    protected $appends = [
        'status_text'
    ];

    protected $hidden = [
        'addresses', 'invoices', 'partner'
    ];

    const STATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;

    protected static $prefix = 'USER';

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 10, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::$prefix);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function partner()
    {
        return $this->hasOne('App\Models\Partner', 'code', 'partner_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function addresses()
    {
        return $this->hasMany('App\Models\UserAddresses', 'user_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function invoices()
    {
        return $this->hasMany('App\Models\UserInvoice', 'user_code', 'code');
    }

    /**
     * @return mixed
     */

    public function getDefaultAddressesAttribute()
    {
        return $this->addresses->firstWhere('status', UserAddresses::STATUS_DEFAULT);
    }

    /**
     * @return mixed
     */

    public function getDefaultInvoicesAttribute()
    {
        return $this->invoices->firstWhere('is_default', PublicDefinition::SWITCH_YES);
    }

    /**
     *
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($maxModel = self::orderByDesc('code')->first()) {
            return substr($maxModel['code'], strlen(self::$prefix));
        }
        return 0;
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return PublicDefinition::getStatusLabel($this->status);
    }

    /**
     * @return bool
     */

    public function getIsPartnerAttribute()
    {
        if (empty($this->partner)) {
            return PublicDefinition::SWITCH_NO;
        }
        return PublicDefinition::SWITCH_YES;
    }

    /**
     * @return mixed
     */

    public function getPartnerStatusAttribute()
    {
        return optional($this->partner)->status;
    }

    /**
     * @return string
     */

    public function getOpenIdAttribute()
    {
        return $this->userOauth->open_id;
    }

    /**
     * @return array
     */

    public function current()
    {
        return [
            "code" => $this->code,
            "avatar" => $this->avatar,
            "nickname" => $this->nickname,
            "partner" => [
                "isBind" => $this->is_partner,
                "code" => empty($this->partner) ? '' : $this->partner->code,
                "totalKMoney" => empty($this->partner) ? 0 : $this->partner->total_get_integral,
                "availableKMoney" => empty($this->partner) ? 0 : $this->partner->available_integral,
                "hasWithdrawKMoney" => empty($this->partner) ? 0 : $this->partner->has_withdraw_integral,
                "waitSettlementKMoney" => empty($this->partner) ? 0 : $this->partner->lock_integral,
                "myTeamCount" => empty($this->partner) ? 0 : $this->partner->team_count,
                "saleOrders" => empty($this->partner) ? 0 : $this->partner->sale_orders_number,
                "monthSales" => empty($this->partner) ? 0 : $this->partner->month_sales_number,
                "yearSales" => empty($this->partner) ? 0 : $this->partner->year_sales_number,
                "inviteCode" => empty($this->partner) ? '' : $this->partner->invite_code,
                "shareCodeUrl" => empty($this->partner) ? '' : WeChatService::generateCode('U:' . $this->partner->code, 'pages/index/main')
            ],
            "orders" => [
                "waitPay" => Order::whereCreateUserCode($this->code)->whereStatus(Order::STATUS_UNPAID)->count(),
                "waitSend" => Order::whereCreateUserCode($this->code)->whereStatus(Order::STATUS_NOT_SHIPPED)->count(),
                "hasSend" => Order::whereCreateUserCode($this->code)->whereStatus(Order::STATUS_UNRECEIVED)->count(),
                "refunds" => RefundOrder::whereUserCode($this->code)->where('status', '!=', RefundOrder::STATUS_FINISH)->count()
            ]
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'code';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {

    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * 关联账号
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function userOauth()
    {
        return $this->hasOne('App\Models\UserOauth', 'user_code', 'code')
            ->where('type', UserOauth::TYPE_WX_APPLETS);
    }
}
