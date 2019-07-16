<?php

namespace App\ModelEvents\Admin;

use App\Models\Admin;
use App\Models\PublicDefinition;
use ZhiEq\Exceptions\CustomException;

class DisabledAdmin
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
        if (!Admin::whereType(Admin::TYPE_SUPER_ADMINISTRATOR)
            ->where('code', '!=', $model->code)
            ->where('status', PublicDefinition::STATUS_ENABLED)->exists()) {
            throw new CustomException('最后一个超级管理员不能禁用');
        }
        $this->model = $model;
    }
}
