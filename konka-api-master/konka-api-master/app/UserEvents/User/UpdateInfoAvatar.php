<?php

namespace App\UserEvents\User;


class UpdateInfoAvatar
{
    /**
     * @var string $iv
     */

    public $iv;

    /**
     * @var string $encryptData
     */

    public $encryptData;

    /**
     * UpdateInfoAvatar constructor.
     * @param $encryptData
     * @param $iv
     */

    public function __construct($encryptData, $iv)
    {
        $this->encryptData = $encryptData;
        $this->iv = $iv;
    }
}