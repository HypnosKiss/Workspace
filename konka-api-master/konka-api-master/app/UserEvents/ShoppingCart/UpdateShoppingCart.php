<?php

namespace App\UserEvents\ShoppingCart;


use App\Models\ShoppingCart;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class UpdateShoppingCart
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * @var ShoppingCart|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $shoppingCartModel;

    /**
     * UpdateShoppingCart constructor.
     * @param $input
     * @param $id
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $id)
    {
        if (!$this->shoppingCartModel = ShoppingCart::whereUserCode(auth_user()->code)->whereId($id)->first()) {
            throw new CustomException('编码无效');
        }
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }


    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'productSpecificationCode' => ['required', Rule::exists('product_specifications', 'code')->where(function (Builder $query) {
                $query->where('stock', '>', 0);
            })],
            'num' => ['required', 'numeric']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'productSpecificationCode.required' => '产品编码不能为空',
            'productSpecificationCode.exists' => '产品编码不存在',
            'num.required' => '数量不能为空',
            'num.numeric' => '数量必须是数量'
        ];
    }
}