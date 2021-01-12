<?php

namespace Crontab;

spl_autoload_register(function($class){
	if(strpos($class, __NAMESPACE__) === 0){
		$f    = substr($class, strlen(__NAMESPACE__));
		$f    = str_replace('\\', '/', $f);
		$file = __DIR__."/$f.php";
		if(is_file($file)){
			require_once $file;
		}
	}
});