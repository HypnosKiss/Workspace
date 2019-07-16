<?php

namespace App\ModelEvents\CommissionRules;

use App\Models\CommissionRule;

class EnabledRule
{
    /**
     * @var CommissionRule
     */

    public $model;

    /**
     * EnabledRule constructor.
     * @param CommissionRule $model
     */

    public function __construct(CommissionRule $model)
    {
        $this->model = $model;
    }
}
