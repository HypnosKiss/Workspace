<?php

namespace App\Http\Middleware;

use App\Models\WxAppletsSession;
use Closure;
use Illuminate\Http\Request;
use ZhiEq\Contracts\MiddlewareExceptRoute;
use ZhiEq\Exceptions\CustomException;

class WxAppletsSessionAutoInit extends MiddlewareExceptRoute
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function subHandle($request, Closure $next)
    {
        if (!$appletsId = $request->header('X-Ca-Applets-Id')) {
            return $next($request);
        }
        if (!WxAppletsSession::getConfigs($appletsId)) {
            throw new CustomException('Unknown Applets Id');
        }
        if (!$sessionId = $request->header('X-Ca-Applets-Session-Id')) {
            throw new CustomException('WxApplets Request Must Need Session Id');
        }
        WxAppletsSession::init($appletsId, $sessionId);
        return $next($request);
    }
}
