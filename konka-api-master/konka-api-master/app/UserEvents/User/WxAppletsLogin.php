<?php

namespace App\UserEvents\User;


use App\Models\User;

class WxAppletsLogin
{
    /**
     * @var User
     */

    public $user;

    /**
     * @var String
     */

    public $encryptData;

    /**
     * @var String
     */

    public $iv;

    /**
     * @var array
     */

    public $mobile;

    /**
     * WxAppletsLogin constructor.
     * @param $encryptData
     * @param $iv
     */

    public function __construct($encryptData, $iv)
    {
        $this->encryptData = $encryptData;
        $this->iv = $iv;
    }
}