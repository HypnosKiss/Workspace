<?php

namespace App\ModelEvents\Admin;

use App\Models\Admin;

class EnabledAdmin
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
        $this->model = $model;
    }
}
