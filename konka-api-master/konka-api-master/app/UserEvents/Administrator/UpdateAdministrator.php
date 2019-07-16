<?php

namespace App\UserEvents\Administrator;


use App\Models\Admin;
use App\Models\PublicDefinition;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class UpdateAdministrator
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * @var Admin $adminModel
     */

    public $adminModel;

    /**
     * CreateAdministrator constructor.
     * @param $input
     * @param $code
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $code)
    {
        if (!$this->adminModel = Admin::whereCode($code)->first()) {
            throw new CustomException('编码不存在');
        }
        Validator::validate($input, $this->rules($code), $this->message());
        $this->input = $input;
    }

    /**
     * @param $code
     * @return array
     */

    protected function rules($code)
    {
        return [
            'username' => ['required', Rule::unique('admins', 'username')->ignore($code, 'code')],
            'nickname' => ['required'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in(PublicDefinition::getPermissionList())],
            'roles' => ['nullable', 'array'],
            'roles.*' => [Rule::exists('roles', 'code')->where('status', PublicDefinition::STATUS_ENABLED)]
        ];
    }

    /**
     * @return array
     */

    protected function message()
    {
        return [
            'username.required' => '用户名不能为空',
            'username.unique' => '用户名已存在',
            'nickname.required' => '姓名不能为空',
            'permissions.array' => '授权权限只能是数组',
            'permissions.*.in' => '权限不正确',
            'roles.array' => '授权角色只能是数组',
            'roles.*.exists' => '角色不存在'
        ];
    }
}
