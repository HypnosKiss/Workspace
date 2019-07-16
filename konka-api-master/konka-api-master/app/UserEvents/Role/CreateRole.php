<?php

namespace App\UserEvents\Role;


use App\Models\Role;

class CreateRole
{
    /**
     * @var array
     */
    public $input;

    /**
     * @var Role
     */

    public $roleModel;

    /**
     * CreateRole constructor.
     * @param array $input
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct(array $input)
    {
        \Validator::validate($input, [
            'name' => ['required'],
            'permissions' => ['required', 'array', 'min:1']
        ], [
            'name.required' => '角色名称不能为空',
            'permissions.required' => '至少选择一项角色权限',
            'permissions.array' => '角色权限必须为数组
            ',
            'permissions.min' => '至少选择一项角色权限',
        ]);
        $this->input = $input;
    }
}
