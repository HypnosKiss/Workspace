<?php

namespace App\UserEvents\Invoice;

use App\Models\OrderInvoice;
use App\Models\PublicDefinition;
use Illuminate\Validation\Rule;
use Validator;

class CreateInvoice
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * CreateInvoice constructor.
     * @param $input
     */

    public function __construct($input)
    {
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
            'materialType' => ['required'],
            'type' => ['required'],
            'unitName' => ['required']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'invoiceType.required' => '发票类型不能为空',
            'invoiceType.in' => '发票类型不正确',
            'materialType.required' => '材质类型不能为空',
            'materialType.in' => '材质类型不正确',
            'type.required' => '抬头类型不能为空',
            'type.in' => '抬头类型不正确',
            'unitName.required' => '抬头不能为空',
        ];
    }
}
