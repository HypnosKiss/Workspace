<?php

namespace swoole;

$path = '/www';
set_include_path(get_include_path().PATH_SEPARATOR.$path); //设置加载根目录路径

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
