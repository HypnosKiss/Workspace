<?php

namespace App\Http\Controllers;

use App\Models\OrderInvoice;
use App\Models\PublicDefinition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;

class InvoiceController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(OrderInvoice::query())
            ->withSearch([
                [
                    'key' => ['orderCode' => 'order_code'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['unitName' => 'unit_name'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => ['taxTicket' => 'tax_ticket'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_LIKE
                ],
                [
                    'key' => 'type',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ],
                [
                    'key' => ['invoiceType' => 'invoice_type'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ],
                [
                    'key' => ['materialType' => 'material_type'],
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ],
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ]
            ])
            ->withPage()
            ->paginateList());
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = OrderInvoice::whereOrderCode($code)->first()) {
            return errors('不存在的开票信息');
        }
        return success($info->toArray());
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo($code, Request $request)
    {
        if (!$info = OrderInvoice::whereOrderCode($code)->first()) {
            return errors('不存在的开票信息');
        }
        $rules = [
            'unitName' => ['required'],
            'type' => ['required', Rule::in(PublicDefinition::getInvoiceTypeList())],
            'taxTicket' => ['required_if:type,' . PublicDefinition::INVOICE_TYPE_COMPANY],
            'invoiceType' => ['required', Rule::in(OrderInvoice::getInvoiceTypeList())],
            'taxTicketAddress' => ['required_if:invoiceType,' . OrderInvoice::INVOICE_TYPE_SPECIAL],
            'taxTicketPhone' => ['required_if:invoiceType,' . OrderInvoice::INVOICE_TYPE_SPECIAL],
            'openBank' => ['required_if:invoiceType,' . OrderInvoice::INVOICE_TYPE_SPECIAL],
            'bankAccount' => ['required_if:invoiceType,' . OrderInvoice::INVOICE_TYPE_SPECIAL],
            'materialType' => ['required', Rule::in(OrderInvoice::getMaterialTypeList())],
            'province' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_PAPER],
            'city' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_PAPER],
            'county' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_PAPER],
            'address' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_PAPER],
            'name' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_PAPER],
            'phone' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_PAPER],
            'sendEmail' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_ELECTRONIC],
            'sendMobile' => ['required_if:materialType,' . OrderInvoice::MATERIAL_TYPE_ELECTRONIC],

        ];
        $messages = [
            'unitName.required' => '抬头不能为空',
            'type.required' => '抬头类型不能为空',
        ];
        $this->validate($request, $rules, $messages);
        $fillKeys = array_keys($rules);
        if (!$info->fillable(snake_case_array($fillKeys))
            ->fill(snake_case_array_keys($request->only($fillKeys)))
            ->save()
        ) {
            return errors('修改失败');
        }
        return success([], '修改成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putBegin($code)
    {
        if (!$info = OrderInvoice::whereOrderCode($code)->first()) {
            return errors('不存在的开票信息');
        }
        if (!$info->toBeing()) {
            return errors('开票失败');
        }
        return success([], '开票成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putConfirm($code)
    {
        if (!$info = OrderInvoice::whereOrderCode($code)->first()) {
            return errors('不存在的开票信息');
        }
        if (!$info->toConfirm()) {
            return errors('确认失败');
        }
        return success([], '确认成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTypeList()
    {
        return success(definition_to_select(PublicDefinition::getInvoiceTypeLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInvoiceTypeList()
    {
        return success(definition_to_select(OrderInvoice::getInvoiceTypeLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getMaterialTypeTextList()
    {
        return success(definition_to_select(OrderInvoice::getMaterialTypeLabels()));
    }
}
