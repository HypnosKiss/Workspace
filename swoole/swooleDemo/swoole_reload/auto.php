<?php

//自动加载
spl_autoload_register(function($class){
     include_once  __DIR__."/{$class}.php";
});



