<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckPartner;
use App\Http\Middleware\FilterVerifyApiSignature;
use App\Http\Middleware\WxAppletsSessionAutoInit;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use ZhiEq\CaseJson\Middleware\CamelCaseInputJson;
use ZhiEq\CaseJson\Middleware\CamelCaseOutputJson;
use ZhiEq\CaseJson\Middleware\CaseOutputJson;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'api' => [
            FilterVerifyApiSignature::class,
            WxAppletsSessionAutoInit::class,
            CamelCaseInputJson::class,
            CamelCaseOutputJson::class,
        ],
        'admin' => [
            FilterVerifyApiSignature::class,
            CamelCaseInputJson::class,
            CaseOutputJson::class,
        ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'wxAppletsSessionAutoInit' => WxAppletsSessionAutoInit::class,
        'checkPartner' => CheckPartner::class
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        FilterVerifyApiSignature::class,
        WxAppletsSessionAutoInit::class,
        CamelCaseInputJson::class,
        Authenticate::class,
        CamelCaseOutputJson::class,
    ];
}
