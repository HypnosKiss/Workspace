<?php
namespace app\index\controller;

use think\Request;

class Index
{
    public function index(Request $request )
    {

         /*
           ob_start();
           dump($request->action());
           ob_end_clean();
         */
        dump($request->get());

        //var_dump($GLOBALS['redis']);
         return 'peter';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
