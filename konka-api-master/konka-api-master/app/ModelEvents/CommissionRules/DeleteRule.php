<?php

namespace App\ModelEvents\CommissionRules;

use App\Models\CommissionRule;

class DeleteRule
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
