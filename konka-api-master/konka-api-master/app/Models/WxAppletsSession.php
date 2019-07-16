<?php

namespace App\Models;

use Carbon\Carbon;

class WxAppletsSession
{
    protected static $cacheKey;

    protected static $cacheData;

    protected static $appletsId;

    protected static $sessionId;


    /**
     * @param $appletsId
     * @param $sessionId
     */

    public static function init($appletsId, $sessionId)
    {
        self::$appletsId = $appletsId;
        self::$sessionId = $sessionId;
        self::$cacheKey = $appletsId . ':' . $sessionId;
    }

    /**
     * @return \Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Exception
     */

    public static function getData()
    {
        if (self::$cacheData === null) {
            self::$cacheData = cache()->get(self::$cacheKey);
        }
        return self::$cacheData;
    }

    /**
     * @param $key
     * @return null
     * @throws \Exception
     */

    public static function getKey($key)
    {
        return isset(self::getData()[$key]) ? self::getData()[$key] : null;
    }

    /**
     * @param array $array
     * @throws \Exception
     */

    public static function setData(array $array)
    {
        self::$cacheData = $array;
        cache()->put(self::$cacheKey, self::$cacheData, Carbon::now()->addMinutes(60));
    }

    /**
     * @return mixed
     * @throws \Exception
     */

    public static function getSessionKey()
    {
        return self::getKey('session_key');
    }

    /**
     * @param null $appletsId
     * @return array
     */

    public static function getConfigs($appletsId = null)
    {
        $appletsId = $appletsId === null ? self::$appletsId : $appletsId;
        $configList = array_filter(config('wx-applets.apps'), function ($config) use ($appletsId) {
            return $config['id'] === $appletsId;
        });
        return empty($configList) ? [] : $configList[0];
    }

    /**
     * @return mixed
     * @throws \Exception
     */

    public static function getOpenid()
    {
        return self::getKey('openid');
    }

    /**
     * @return null
     * @throws \Exception
     */

    public static function getUnionId()
    {
        return self::getKey('unionid');
    }

    /**
     * @return mixed|null
     */

    public static function getAppId()
    {
        return isset(self::getConfigs()['app_id']) ? self::getConfigs()['app_id'] : null;
    }

    /**
     * @return mixed|null
     */

    public static function getAppSecret()
    {
        return isset(self::getConfigs()['app_secret']) ? self::getConfigs()['app_secret'] : null;
    }

    /**
     * @return bool
     */

    public static function isInit()
    {
        return self::$appletsId !== null && self::$cacheKey !== null;
    }
}
