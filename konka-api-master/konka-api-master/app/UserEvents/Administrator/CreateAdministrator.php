<?php

namespace App\UserEvents\Administrator;


use App\Models\Admin;
use App\Models\PublicDefinition;
use Illuminate\Validation\Rule;
use Validator;

class CreateAdministrator
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
            'username' => ['required', Rule::unique('admins', 'username')],
            'password' => ['required'],
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
            'password.required' => '密码不能为空',
            'nickname.required' => '姓名不能为空',
            'permissions.array' => '授权权限只能是数组',
            'permissions.*.in' => '权限不正确',
            'roles.array' => '授权角色只能是数组',
            'roles.*.exists' => '角色不存在'
        ];
    }
}
