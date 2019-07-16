<?php

namespace App\UserEvents\Addresses;


use App\Models\UserAddresses;
use ZhiEq\Exceptions\CustomException;

class UpdateStatus
{

    /**
     * @var UserAddresses|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $addressesModel;

    /**
     * UpdateStatus constructor.
     * @param $code
     */

    public function __construct($code)
    {
        if (!$this->addressesModel = UserAddresses::whereCode($code)->first()) {
            throw new CustomException('编码无效');
        }
    }
}