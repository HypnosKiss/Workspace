<?php

namespace App\UserEvents\ShoppingCart;


use Validator;

class BatchDelete
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * BatchDelete constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    /**
     * @return array
     */

    protected function rules()
    {
        return [
            'ids' => ['required', 'array']
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'ids.required' => 'ids不能为空',
            'ids.array' => 'ids必须是数组'
        ];
    }
}