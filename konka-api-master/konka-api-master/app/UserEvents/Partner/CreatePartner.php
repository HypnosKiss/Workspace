<?php

namespace App\UserEvents\Partner;

use App\Models\Partner;
use ZhiEq\Exceptions\CustomException;

class CreatePartner
{
    /**
     * @var array
     */

    public $input;

    /**
     * @var String
     */

    public $type;

    /**
     * CreatePartner constructor.
     * @param $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input)
    {
        if (!isset($input['type']) || !in_array($input['type'], Partner::importType())) {
            throw new CustomException('合伙人类型不能为空');
        }
        $this->type = (int)$input['type'];
        \Validator::validate($input, $this->rules(), $this->messages());
        $this->input = $input;
    }

    /**
     * @return array
     */

    protected function rules()
    {
        if ($this->type === Partner::TYPE_R3_CLIENT) {
            return [
                'companyName' => ['required'],
                'r3Code' => ['required'],
                'mergeCode' => ['required'],
                'clientName' => ['required'],
            ];
        } elseif ($this->type === Partner::TYPE_NETWORK_CLIENT) {
            return [
                'companyName' => ['required'],
                'handingName' => ['required'],
                'networkName' => ['required'],
                'networkNumber' => ['required'],
                'parentClientName' => ['required'],
                'parentClientCode' => ['required'],
            ];
        } elseif (in_array($this->type, [Partner::TYPE_INTERNAL_STAFF, Partner::TYPE_COOPERATION])) {
            return [
                'activationCode' => ['required'],
                'inlineName' => ['required'],
                'inlineNumber' => ['required'],
                'firstDepartment' => ['required'],
                'secondDepartment' => ['required'],
            ];
        } else {
            return [];
        }
    }

    /**
     * @return array
     */

    protected function messages()
    {
        return [
            'type.required' => '合伙人类型不能为空',
            'type.in' => '合伙人类型不争取',
            'area.required' => '区域信息不能为空',
            'companyName.required' => '分公司不能为空',
            'r3Code.required' => 'R3编码不能为空',
            'clientName.required' => '客户名称不能为空',
            'mergeCode.required' => '合并编码不能为空',
            'clientType.required' => '客户类型不能为空',
            'companyAddress.required' => '客户地址不能为空',
            'handingName.required' => '经办名称不能为空',
            'networkName.required' => '网点名称不能为空',
            'networkNumber.required' => '网点编号不能为空',
            'parentClientName.required' => '上级客户不能为空',
            'parentClientCode.required' => '上级客户编码不能为空',
            'activationCode.required' => '激活码不能为空',
            'inlineName.required' => '员工姓名不能为空',
            'inlineNumber.required' => '电话/工卡号不能为空',
            'firstDepartment.required' => '一级部门不能为空',
            'secondDepartment.required' => '二级部门不能为空',
        ];
    }
}
