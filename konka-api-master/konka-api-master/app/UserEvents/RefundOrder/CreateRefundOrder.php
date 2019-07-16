<?php

namespace App\UserEvents\RefundOrder;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\RefundOrder;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class CreateRefundOrder
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * @var Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $orderModel;

    /**
     * @var RefundOrder $refundOrderModel
     */

    public $refundOrderModel;

    /**
     * @var OrderProduct
     */

    public $productModel;

    /**
     * CreateRefundOrder constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __construct($input)
    {
        Validator::validate($input, $this->rules(), $this->message());
        if (!$this->orderModel = Order::whereCode($input['orderCode'])->first()) {
            throw new CustomException('订单不存在');
        }
        $product = collect($this->orderModel->products)->filter(function ($product) use ($input) {
            return $product['product_code'] === $input['productCode'];
        });
        if ($product->count() <= 0) {
            throw new CustomException('产品编码不存在');
        }
        if ((int)$product->first()['num'] < (int)$input['num']) {
            throw new CustomException('退换货数量不正确');
        }
        if (!$this->productModel = $this->orderModel->orderProduct->where('product_code', $input['productCode'])->first()) {
            throw new CustomException('产品数据不完整');
        }
        $this->input = $input;
    }

    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'type' => ['required', Rule::in(RefundOrder::getTypeList())],
            'refundType' => ['required', Rule::in(RefundOrder::getRefundTypeList())],
            'content' => ['required'],
            'num' => ['required', 'integer'],
            'images' => ['nullable', 'array'],
            'orderCode' => ['required'],
            'productCode' => ['required']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'type.required' => '类型不能为空',
            'type.in' => '类型不正确',
            'refundType.required' => '退货方式不能为空',
            'refundType.in' => '退货方式不正确',
            'content.required' => '问题描述不能为空',
            'images.array' => '问题描述图片必须是数组',
            'price.required' => '退货金额不能为空',
            'price.numeric' => '退货金额必须是数值',
            'products.required' => '产品不能为空',
            'products.array' => '产品必须是数组',
            'products.min' => '产品必须有一个',
            'products.*.orderProductId.required' => '订单产品不能为空',
            'products.*.orderProductId.exists' => '订单产品不存在',
            'products.*.num.required' => '订单产品数量不能为空',
            'products.*.num.numeric' => '订单产品数量必须是数值',
            'products.*.num.min' => '订单产品数量最小等于1',
        ];
    }
}
