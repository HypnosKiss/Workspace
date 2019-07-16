<?php

namespace App\UserEvents\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductSpecification;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Validator;

class CreateOrder
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * @var Order $orderModel
     */
    public $orderModel;

    /**
     * @var ProductSpecification[]|\Illuminate\Database\Eloquent\Collection
     */

    public $productSpecifications;

    /**
     * @var OrderProduct[]
     */

    public $orderProduct;

    /**
     * @var int $commission 佣金
     */

    public $commission;

    /**
     * CreateOrder constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        Validator::validate($input, $this->rules(), $this->message());
        $this->productSpecifications = ProductSpecification::whereIn('code', collect($input['products'])->pluck('productSpecificationCodes')->toArray())->get();
        $this->input = $input;
    }

    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'distribution' => ['required', Rule::in(Order::getDistributionList())],
            'addressesCode' => ['required', Rule::exists('user_addresses', 'code')->where(function (Builder $query) {
                $query->where('user_code', auth_user()->code);
            })],
            'invoiceCode' => ['nullable', Rule::exists('user_invoices', 'code')->where(function (Builder $query) {
                $query->where('user_code', auth_user()->code);
            })],
            'products' => ['required', 'array', 'min:1'],
            'freight' => ['required', 'numeric'],
            'products.*.productSpecificationCodes' => ['required'],
            'products.*.num' => ['required', 'numeric', 'min:1'],
            'actuallyPayPrice' => ['required', 'numeric'],
            'payType' => ['required', Rule::in(Order::getPayTypeList())]
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'distribution.required' => '配送方式不能为空',
            'distribution.in' => '配送方式不正确',
            'addressesCode.required' => '地址不能为空',
            'addressesCode.exists' => '地址不存在',
            'invoiceCode.exists' => '发票不存在',
            'freight.required' => '运费不能为空',
            'freight.numeric' => '运费必须是数字',
            'products.required' => '产品不能为空',
            'products.array' => '产品必须是数组',
            'products.min' => '产品必须有一个',
            'products.*.productSpecificationCodes.required' => '产品规格不能为空',
            'products.*.productSpecificationCodes.exists' => '产品规格不存在',
            'products.*.num.required' => '产品数量不存在',
            'products.*.num.numeric' => '产品数量必须是数字',
            'products.*.num.min' => '产品数量最少一个',
            'actuallyPayPrice.required' => '实付金额不能为空',
            'actuallyPayPrice.numeric' => '实付金额必须是数字'
        ];
    }
}
