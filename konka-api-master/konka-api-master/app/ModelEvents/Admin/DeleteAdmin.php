<?php

namespace App\ModelEvents\Admin;

use App\Models\Admin;
use ZhiEq\Exceptions\CustomException;

class DeleteAdmin
{
    /**
     * @var Admin
     */

    public $model;

    /**
     * EnabledRule constructor.
     * @param Admin $model
     */

    public function __construct(Admin $model)
    {
        if (!Admin::whereType(Admin::TYPE_SUPER_ADMINISTRATOR)->where('code', '!=', $model->code)->exists()) {
            throw new CustomException('最后一个超级管理员不能删除');
        }
        $this->model = $model;
    }
}
