<?php

namespace swoole;

$path = '/www';
set_include_path(get_include_path().PATH_SEPARATOR.$path); //设置加载根目录路径
require_once 'vendor/autoload.php';
require_once 'server_config/ServerConfig.php';

use Lite\Core\Application;
use Lite\DB\Driver\DBAbstract;

!defined('PACKAGE_LENGTH_TYPE') && define('PACKAGE_LENGTH_TYPE', 'N');
!defined('PACKAGE_LENGTH_OFFSET') && define('PACKAGE_LENGTH_OFFSET', '0');//8
!defined('PACKAGE_BODY_OFFSET') && define('PACKAGE_BODY_OFFSET', '4');    //16

spl_autoload_register(function($class){
	if(strpos($class, __NAMESPACE__) === 0){
		$file    = substr($class, strlen(__NAMESPACE__));
		$file    = str_replace('\\', '/', $file);
		$file = __DIR__."/$file.php";
		if(is_file($file)){
			require_once $file;
		}
	}
});
$levels    = 1;
$namespace = 'sales\sale';
$erp_root  = dirname(__DIR__, $levels).'/erp/';
$app_root  = dirname(__DIR__, $levels).'/sale/';
try{
	Application::init($namespace, $app_root, Application::MODE_CLI);
	Application::addIncludePath($erp_root.'app/', 'sales\\');
	Application::addIncludePath($erp_root.'app/include/', 'sales\\');
	DBAbstract::distinctQueryOff();
}catch(\Exception $exception){
	die($exception);
}
