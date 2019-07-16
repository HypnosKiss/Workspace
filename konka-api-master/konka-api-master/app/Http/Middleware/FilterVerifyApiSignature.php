<?php

namespace App\Http\Middleware;


use ZhiEq\ApiSignature\Middleware\VerifyApiSignature;

class FilterVerifyApiSignature extends VerifyApiSignature
{
    protected $expectRoute = [
        '/api/upload-image'
    ];
}