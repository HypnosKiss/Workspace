<?php

namespace App\UserEvents\CustomerServiceMessage;


use App\Models\CustomerServiceMessage;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class ReplyCustomerServiceMessage
{
    public $input;

    /**
     * @var CustomerServiceMessage|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $customerServiceMessageModel;

    /**
     * CreateCustomerServiceMessage constructor.
     * @param $input
     * @param $id
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $id)
    {
        if (!$this->customerServiceMessageModel = CustomerServiceMessage::whereId($id)->first()) {
            throw new CustomException('ID无效');
        }
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    protected function rules()
    {
        return [
            'content' => ['required'],
            'messageType' => ['required', Rule::in([CustomerServiceMessage::MESSAGE_TYPE_STRING, CustomerServiceMessage::MESSAGE_TYPE_IMAGE])]
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