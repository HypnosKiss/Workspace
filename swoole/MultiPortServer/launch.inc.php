<?php

namespace swoole;

require_once 'vendor/autoload.php';
require_once 'server_config/ServerConfig.php';

use Lite\Core\Application;
use Lite\DB\Driver\DBAbstract;

$levels    = 1;
$namespace = 'sales\swoole';
$erp_root  = dirname(__DIR__, $levels).'/erp/';
$sale_root = dirname(__DIR__, $levels).'/sale/';
$app_root  = dirname(__DIR__, $levels).'/swoole/';

try{
	Application::init($namespace, $app_root, Application::MODE_CLI);
	Application::addIncludePath($erp_root.'app/', 'sales\\');
	Application::addIncludePath($erp_root.'app/include/', 'sales\\');
	Application::addIncludePath($sale_root.'app/', 'sales\\sale\\');
	Application::addIncludePath($sale_root.'app/include/', 'sales\\sale\\');
	DBAbstract::distinctQueryOff();
}catch(\Exception $exception){
	die($exception);
}