<?php

namespace App\UserEvents\CustomerServiceMessage;


use App\Models\CustomerServiceMessage;
use Illuminate\Validation\Rule;
use Validator;

class CreateCustomerServiceMessage
{
    public $input;

    /**
     * CreateCustomerServiceMessage constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    protected function rules()
    {
        return [
            'content' => ['required'],
            'messageType' => ['required', Rule::in(CustomerServiceMessage::getMessageTypeList())]
        ];
    }

    protected function message()
    {
        return [
            'content.required' => '内容不能为空',
            'messageType.required' => '内容类型不能为空',
            'messageType.in' => '内容类型不正确',
        ];
    }
}
