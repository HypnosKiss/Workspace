<?php

namespace App\Models;

use App\ModelEvents\UserInvoice\DeleteInvoice;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;
use ZhiEq\Utils\Trigger;

/**
 * App\Models\UserInvoice
 *
 * @property int $id
 * @property string $code 编码
 * @property string $user_code 用户编码
 * @property int|null $type 类型
 * @property string $unit_name 名称
 * @property string|null $tax_ticket 税票号
 * @property string|null $tax_ticket_address 税票地址
 * @property string|null $tax_ticket_phone 税票电话
 * @property string|null $open_bank 开户行
 * @property string|null $bank_account 银行帐号
 * @property int $is_default 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $invoice_type 发票类型
 * @property int $material_type 材质类型:电子/纸质
 * @property string|null $name 收货人名称
 * @property string|null $phone 收货人电话
 * @property string|null $province 省中文
 * @property string|null $city 市中文
 * @property string|null $county 县中文
 * @property string|null $address 详细地址
 * @property string|null $send_email 电子发票发送电子邮箱
 * @property string|null $send_mobile 电子发票通知手机号码
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereInvoiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereMaterialType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereOpenBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereSendEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereSendMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereTaxTicket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereTaxTicketAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereTaxTicketPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInvoice whereUserCode($value)
 * @mixin \Eloquent
 * @property-read mixed $invoice_type_text
 * @property-read string $is_default_text
 * @property-read mixed $material_type_text
 * @property-read mixed $type_text
 */
class UserInvoice extends Model
{
    use DefinitionAttribute;

    protected $attributes = [
        'is_default' => PublicDefinition::SWITCH_NO
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 3, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'INVOICE';
    }

    /**
     *
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($maxModel = self::orderByDesc('code')->first()) {
            return substr($maxModel['code'], strlen(self::codePrefix()));
        }
        return 0;
    }

    /**
     * @return bool
     */

    public function toDelete()
    {
        return Trigger::eventWithTransaction(new DeleteInvoice($this));
    }

    /**
     * @return mixed
     */

    public function getInvoiceTypeTextAttribute()
    {
        return OrderInvoice::getInvoiceTypeLabel($this->invoice_type);
    }

    /**
     * @return mixed
     */

    public function getTypeTextAttribute()
    {
        return PublicDefinition::getInvoiceTypeLabel($this->type);
    }

    /**
     * @return mixed
     */

    public function getMaterialTypeTextAttribute()
    {
        return OrderInvoice::getMaterialTypeLabel($this->material_type);
    }

    /**
     * @return string
     */

    public function getIsDefaultTextAttribute()
    {
        return PublicDefinition::getSwitchLabel($this->is_default);
    }
}
