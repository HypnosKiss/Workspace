<?php

namespace App\ModelEvents\Partner;

use App\Models\Partner;

class DowngradePartner
{
    /**
     * @var Partner
     */

    public $model;

    /**
     * DeletePartner constructor.
     * @param Partner $model
     */

    public function __construct(Partner $model)
    {
        $this->model = $model;
    }
}
