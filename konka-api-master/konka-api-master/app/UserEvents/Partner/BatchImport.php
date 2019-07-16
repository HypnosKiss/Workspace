<?php

namespace App\UserEvents\Partner;

use App\Extend\FastValidator;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Validation\Rule;
use ZhiEq\Exceptions\CustomException;

class BatchImport
{
    /**
     * @var array $data
     */
    public $input;

    /**
     * @var User[]|\Illuminate\Database\Eloquent\Collection $userModels
     */
    public $userModels;

    /**
     * BatchImport constructor.
     * @param $input
     * @throws \Exception
     */

    public function __construct($input)
    {
        $errors = FastValidator::make($input, $this->rules())->errors();
        if ($errors->isNotEmpty()) {
            $message = collect($errors->messages())->map(function ($message, $errorKey) {
                list($row, $attribute) = explode('.', $errorKey);
                return ['row' => (int)($row + 1), 'attribute' => ucfirst($attribute), 'message' => collect($message)->first()];
            })->first();
            throw new CustomException('第' . $message['row'] . '行' . $this->message($message['message']));
        }
        $this->input = $input;
    }


    protected function rules()
    {
        return [
            'type' => ['required', 'in:' . implode(',', Partner::importType())],
            'companyName' => 'required_if:type,' . Partner::TYPE_R3_CLIENT . ',' . Partner::TYPE_NETWORK_CLIENT,
            'r3Code' => 'required_if:type,' . Partner::TYPE_R3_CLIENT,
            'mergeCode' => 'required_if:type,' . Partner::TYPE_R3_CLIENT,
            'clientName' => 'required_if:type,' . Partner::TYPE_R3_CLIENT,
            'handingName' => 'required_if:type,' . Partner::TYPE_NETWORK_CLIENT,
            'networkName' => 'required_if:type,' . Partner::TYPE_NETWORK_CLIENT,
            'networkCode' => 'required_if:type,' . Partner::TYPE_NETWORK_CLIENT,
            'parentClientName' => 'required_if:type,' . Partner::TYPE_NETWORK_CLIENT,
            'parentClientCode' => 'required_if:type,' . Partner::TYPE_NETWORK_CLIENT,
            'activationCode' => 'required_if:type,' . Partner::TYPE_INTERNAL_STAFF . ',' . Partner::TYPE_COOPERATION,
            'inlineName' => 'required_if:type,' . Partner::TYPE_INTERNAL_STAFF . ',' . Partner::TYPE_COOPERATION,
            'inlineNumber' => 'required_if:type,' . Partner::TYPE_INTERNAL_STAFF . ',' . Partner::TYPE_COOPERATION,
            'firstDepartment' => 'required_if:type,' . Partner::TYPE_INTERNAL_STAFF . ',' . Partner::TYPE_COOPERATION,
            'secondDepartment' => 'required_if:type,' . Partner::TYPE_INTERNAL_STAFF . ',' . Partner::TYPE_COOPERATION,
        ];
    }

    protected function message($message)
    {
        $map = [
            'companyAddress Required' => '公司地址不能为空',
            'area Required' => '区域不能为空',
            'r3Code Required' => 'R3编码不能为空',
            'clientName Required' => '客户名称不能为空',
            'mergeCode Required' => '合并编码不能为空',
            'companyName Required' => '公司不能为空',
        ];
        return isset($map[$message]) ? $map[$message] : $message;
    }
}
