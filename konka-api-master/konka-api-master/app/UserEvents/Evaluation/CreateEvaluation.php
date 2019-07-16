<?php

namespace App\UserEvents\Evaluation;


use App\Models\Order;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class CreateEvaluation
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
     * CreateEvaluation constructor.
     * @param $input
     * @param $orderCode
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $orderCode)
    {
        if (!$this->orderModel = Order::whereCode($orderCode)->first()) {
            throw new CustomException('订单编码不存在');
        }
        Validator::validate($input, $this->rules($orderCode), $this->message());
        $this->input = $input;
    }

    /**
     * @param $orderCode
     * @return array
     */

    protected function rules($orderCode)
    {
        return [
            'products' => ['required', 'array'],
            'products.*.productCode' => ['required', 'distinct', Rule::exists('order_products', 'product_code')
                ->where(function (Builder $query) use ($orderCode) {
                    $query->where('order_code', $orderCode);
                })],
            'products.*.rate' => ['required', 'numeric'],
            'products.*.content' => ['required'],
            'products.*.images' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'products.required' => '产品不能为空',
            'products.array' => '产品必须是数组',
            'products.*.productCode.required' => '产品编码不能为空',
            'products.*.productCode.distinct' => '产品编码不能重复',
            'products.*.productCode.exists' => '产品编码不存在',
            'products.*.rate.required' => '评分不能为空',
            'products.*.rate.numeric' => '评分必须是数值',
            'products.*.content.required' => '内容不能为空',
            'products.*.images.array' => '图片必须是数组',
        ];
    }
}