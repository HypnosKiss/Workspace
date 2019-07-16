<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use ZhiEq\Contracts\MiddlewareExceptRoute;

class CheckPartner extends MiddlewareExceptRoute
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function subHandle($request, Closure $next)
    {
        if (auth_user()->is_partner === 20) {
            return errors('不是合伙人');
        }
        return $next($request);
    }
}