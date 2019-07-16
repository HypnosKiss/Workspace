<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;


/**
 * App\Models\OrderInvoice
 *
 * @property int $id
 * @property string $order_code 订单编码
 * @property int $invoice_type 发票类型
 * @property int|null $type 类型
 * @property string $unit_name 名称
 * @property string|null $tax_ticket 税票号
 * @property string|null $tax_ticket_address 税票地址
 * @property string|null $tax_ticket_phone 税票电话
 * @property string|null $open_bank 开户行
 * @property string|null $bank_account 银行帐号
 * @property string|null $name 收货人名称
 * @property string|null $phone 收货人电话
 * @property string|null $province 省中文
 * @property string|null $city 市中文
 * @property string|null $county 县中文
 * @property string|null $address 详细地址
 * @property string|null $image 电子发票图片
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $material_type 材质类型:电子/纸质
 * @property string|null $filename 电子发票PDF文件名
 * @property string|null $send_email 电子发票发送电子邮箱
 * @property string|null $send_mobile 电子发票通知手机号码
 * @property-read string $full_address
 * @property-read null|string $image_url
 * @property-read mixed $invoice_type_text
 * @property-read mixed $material_type_text
 * @property-read mixed $status_text
 * @property-read mixed $type_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereInvoiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereMaterialType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereOpenBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereOrderCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereSendEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereSendMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereTaxTicket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereTaxTicketAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereTaxTicketPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInvoice whereUpdatedAt($value)
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 * @method static getInvoiceTypeList()
 * @method static getInvoiceTypeLabels()
 * @method static getInvoiceTypeLabel($key)
 * @method static getTypeList()
 * @method static getTypeLabels()
 * @method static getTypeLabel($key)
 * @method static getMaterialTypeList()
 * @method static getMaterialTypeLabels()
 * @method static getMaterialTypeLabel($key)
 * @mixin \Eloquent
 */
class OrderInvoice extends Model
{
    use DefinitionAttribute;

    //状态
    const STATUS_WAIT = 10;
    const STATUS_ING = 20;
    const STATUS_FINISH = 30;

    //发票材质类型
    const MATERIAL_TYPE_ELECTRONIC = 10;
    const MATERIAL_TYPE_PAPER = 20;

    //发票类型
    const INVOICE_TYPE_NORMAL = 10;//普票
    const INVOICE_TYPE_SPECIAL = 20;//专票

    protected $attributes = [
        'status' => self::STATUS_WAIT
    ];

    protected $appends = [
        'image_url', 'status_text', 'full_address', 'type_text'
    ];

    /**
     * @return array
     */

    protected static function materialTypeDefinition()
    {
        return [
            self::MATERIAL_TYPE_ELECTRONIC => '电子发票',
            self::MATERIAL_TYPE_PAPER => '纸质发票'
        ];
    }

    /**
     * @return array
     */

    protected static function invoiceTypeDefinition()
    {
        return [
            self::INVOICE_TYPE_NORMAL => '普票',
            self::INVOICE_TYPE_SPECIAL => '专票'
        ];
    }

    /**
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_WAIT => '未开票',
            self::STATUS_ING => '开票中',
            self::STATUS_FINISH => '已开票'
        ];
    }

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

    public function getInvoiceTypeTextAttribute()
    {
        return self::getInvoiceTypeLabel($this->invoice_type);
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
        return self::getMaterialTypeLabel($this->material_type);
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

    public function getFullAddressAttribute()
    {
        return $this->province . $this->city . $this->county . $this->address;
    }

    /**
     * @return mixed
     */

    public function toBeing()
    {
        if ($this->status !== self::STATUS_WAIT) {
            return false;
        }
        return $this->setAttribute('status', self::STATUS_ING)->save();
    }

    /**
     * @return mixed
     */

    public function toConfirm()
    {
        if ($this->status !== self::STATUS_ING) {
            return false;
        }
        return $this->setAttribute('status', self::STATUS_FINISH)->save();
    }
}
