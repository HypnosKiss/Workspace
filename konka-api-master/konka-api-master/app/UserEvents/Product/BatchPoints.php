<?php

namespace App\UserEvents\Product;


use App\Models\Product;
use Validator;
use ZhiEq\Exceptions\CustomException;

class BatchPoints
{

    public $input;

    /**
     * @var Product[]|\Illuminate\Database\Eloquent\Collection
     */

    public $productModel;

    /**
     * BatchPoints constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        Validator::validate($input, $this->rules(), $this->message());
        $this->productModel = Product::whereIn('code', $input['codes'])->get();
        if ($this->productModel->isEmpty()) {
            throw new CustomException('请选择产品');
        }
        if ($this->productModel->count() !== count($input['codes'])) {
            throw new CustomException('产品编码不正确');
        }
        $this->input = $input;
    }

    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'points' => ['required', 'numeric'],
            'codes' => ['required', 'array']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'points.required' => '点数不能为空',
            'points.numeric' => '点数必须是数值',
            'codes.required' => '编码集不能为空',
            'codes.array' => '编码集必须是数组'
        ];
    }
}