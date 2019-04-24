<?php


$http=new \swoole\http\server('0.0.0.0',9502);

$http->set([
    'package_max_length'=>1024*1024*10,
    'upload_tmp_dir'=>__DIR__.'/upload'
]);

//监听http协议
$http->on('request',function ($request,$response){

     //var_dump($request->get);
     //var_dump($request->server);

     //var_dump($request->header);
     // $response->end('帅的不行的peter');
     // var_dump('我是post',$request->post);

    //post
     //var_dump($request->rawContent()); //获取原始数据 php://input

    //根据请求头的不同类型,返回相应格式的数据
    if(isset($request->header['content-type']) && $request->header['content-type']=='application/json'){
        var_dump('接收到json格式数据');
         //json_encode();
    }elseif(isset($request->header['content-type']) && $request->header['content-type']=='application/xml'){

    }

    //文件上传
    //$file=$request->files;
    //move_uploaded_file($file['peterFile']['tmp_name'],__DIR__.'/upload/1.txt');

    //设置响应头
//    $response->header('Content-Type','text/html');
//    $response->header('Charset','utf-8');
//
//    $response->status(123);
//
//    $response->cookie('user','peter');
//
//    $response->write('123---'); //分段发送不能使用end
//    $response->write('456'); //分段发送不能使用end
//
//    $response->end('<meta charset="UTF-8"><h2>六星教育</h2>'); //返回请求
    //$response->header('Content-Type', 'image/jpeg');
    //$response->sendfile(__DIR__.'/upload/1.txt');


});

$http->start();


