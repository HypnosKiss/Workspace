<?php

namespace App\UserEvents\Settings;


class SaveSetting
{

    /**
     * @var array
     */

    public $input;

    /**
     * SaveSetting constructor.
     * @param $input
     */

    public function __construct($input)
    {
        $this->input = $input;
    }
}
