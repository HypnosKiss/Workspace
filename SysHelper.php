<?php
/**
 * Created by PhpStorm.
 * User: admin
 */

namespace system\component\helpers;

use Yii;

class SysHelper
{
    /**
     * 读取模块data目录下的数据文件
     * @param string $module 模块名，backend、frontend、system/modules/user
     * @param string $name Data名称
     * @return array
     * */
    static public function getData($module, $name)
    {
        $paramName = $module . '_' . $name;
        $file = Yii::getAlias('@root') . '/' . $module . '/data/' . $name . '.php';
        static $_systemData;
        if (!isset($_systemData[$paramName])) {
            if (file_exists($file)) {
                $_systemData[$paramName] = require_once($file);
                return $_systemData[$paramName];
            }
        } else {
            return $_systemData[$paramName];
        }
    }

    /**
     * 返回上传根地址
     * @param $path string
     * @return string
     */
    static public function getUploadUrl($path = '')
    {
        if (empty($path))
            $path = Yii::getAlias('@uploads');
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', $path));

        return $path;
    }

    /**
     * 查询是否加载模块
     * @param $moduleName string 模块名
     * @return bool
     */
    static public function hasLoadUserModule($moduleName)
    {
        $modules = Yii::$app->getModules();
        $result = in_array($moduleName, array_keys($modules));

        return $result;
    }

    /**
     * 获取当前路径
     * @return string
     */
    static public function getRootUrl()
    {
        $current_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
        if (stristr($current_url, '/system')) {
            $current_url = substr($current_url, -strlen($current_url), strpos($current_url, "/system"));
        } else {
            $current_url = 'http://' . $_SERVER['SERVER_NAME'];
        }
        return $current_url;
    }

    /**
     * 根据坐标获取地址
     * @param $lng
     * @param $lat
     * @return string
     */
    static public function getAddress($lng, $lat)
    {
        $address = '-';
        if (!empty($lng) && !empty($lat)) {
            $address_arr = json_decode(self::curlGetContents('http://restapi.amap.com/v3/geocode/regeo?
        key=b1c9e8b787c2ce5d036cc45044daf215&location=' . $lng . ',' . $lat), true);
            if (isset($address_arr['regeocode']['formatted_address']) && !empty($address_arr['regeocode']['formatted_address'])) {
                $address = $address_arr['regeocode']['formatted_address'];
            }
        }
        return $address;
    }

    /**
     * 远程地址内容请求
     * @param $url
     * @return mixed
     */
    static public function curlGetContents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    /**
     * 测试数据打印
     * */
    static public function p()
    {
        $args = func_get_args();
        $output = '';
        foreach ($args as $arg) {
            @$output .= var_export($arg, true);
        }
        echo '<pre>' . $output . '</pre>';
        exit();
    }

}