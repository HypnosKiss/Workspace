<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use ZhiEq\Contracts\Controller;

class SiteController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function index()
    {
        return success(['now' => Carbon::now()->format('Y-m-d H:i:s')], 'System Is Running.');
    }
}
