<?php

namespace App\UserEvents\Invoice;


use App\Models\OrderInvoice;
use App\Models\PublicDefinition;
use App\Models\UserInvoice;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class UpdateInvoice
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * @var UserInvoice|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $invoiceModel;

    /**
     * UpdateInvoice constructor.
     * @param $input
     * @param $code
     */

    public function __construct($input, $code)
    {
        if (!$this->invoiceModel = UserInvoice::whereCode($code)->first()) {
            throw new CustomException('编码不存在');
        }
        $validator = Validator::make($input, $this->rules(), $this->message());
        //专票只能是纸质发票
        $validator->sometimes('materialType', Rule::in([OrderInvoice::MATERIAL_TYPE_PAPER]), function ($input) {
            return $input['invoiceType'] === OrderInvoice::INVOICE_TYPE_SPECIAL;
        });
        //专票只能是公司抬头
        $validator->sometimes('type', Rule::in([PublicDefinition::INVOICE_TYPE_COMPANY]), function ($input) {
            return $input['invoiceType'] === OrderInvoice::INVOICE_TYPE_SPECIAL;
        });
        //普票材质类型
        $validator->sometimes('materialType', Rule::in(OrderInvoice::getMaterialTypeList()), function ($input) {
            return $input['invoiceType'] === OrderInvoice::INVOICE_TYPE_NORMAL;
        });
        //普票抬头类型
        $validator->sometimes('type', Rule::in(PublicDefinition::getInvoiceTypeList()), function ($input) {
            return $input['invoiceType'] === OrderInvoice::INVOICE_TYPE_NORMAL;
        });
        //纸质发票必须填收件信息
        $validator->sometimes(['name', 'phone', 'province', 'city', 'county', 'address'], 'required', function ($input) {
            return $input['materialType'] === OrderInvoice::MATERIAL_TYPE_PAPER;
        });
        //电子发票必须填接受邮箱和手机号码
        $validator->sometimes(['sendEmail', 'sendMobile'], 'required', function ($input) {
            return $input['materialType'] === OrderInvoice::MATERIAL_TYPE_ELECTRONIC;
        });
        //专票必填填写完整的信息
        $validator->sometimes(['taxTicketAddress', 'taxTicketPhone', 'openBank', 'bankAccount'], 'required', function ($input) {
            return $input['invoiceType'] === OrderInvoice::INVOICE_TYPE_SPECIAL;
        });
        //公司抬头必须填写税号
        $validator->sometimes('taxTicket', 'required', function ($input) {
            return $input['type'] === PublicDefinition::INVOICE_TYPE_COMPANY;
        });
        $validator->validate();
        $this->input = $input;
    }

    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'invoiceType' => ['required', Rule::in(OrderInvoice::getInvoiceTypeList())],
            'materialType' => ['required', Rule::in(OrderInvoice::getMaterialTypeList())],
            'type' => ['required', Rule::in(PublicDefinition::getInvoiceTypeList())],
            'unitName' => ['required']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'status.required' => '状态不能为空',
            'status.in' => '状态不正确',
            'type.required' => '类型不能为空',
            'type.in' => '类型不正确',
            'unitName.required' => '名称不能为空',
            'taxTicket.required' => '税票号不能为空',
            'taxTicketAddress.required' => '税票地址不能为空',
            'taxTicketPhone.required' => '税票电话不能为空',
            'openBank.required' => '开户行不能为空',
            'bankAccount.required' => '银行帐号不能为空',
            'userAddressesCode.required' => '地址编码不能为空',
            'userAddressesCode.exists' => '地址编码不正确',
            'mobile.required' => '手机号码不能为空',
            'email.required' => '常用邮箱不能为空'
        ];
    }
}
