<?php

/**
 * @Desc
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * Created by PhpStorm
 * User: develop41
 * Date: 2018-11-22 15:41:13
 */

class Arrow
{

    static $instance;

    /** * @return static */
    public static function getInstance()
    {

        if (NULL === self::$instance) self::$instance = new self();

        return self::$instance;
    }

    public function run($rb)
    {

        $pid = pcntl_fork();
        if ($pid > 0) {
            pcntl_wait($status);
        } else if ($pid == 0) {
            $cid = pcntl_fork();
            if ($cid > 0) {
                exit();
            } else if ($cid == 0) {
                $rb();
            } else {
                exit();
            }
        } else {
            exit();
        }
    }
}

//离弦之箭---调用方法
Arrow::getInstance()->run(function () {

    //这里写我们要执行的代码
    sleep(30);
});

/**
 * @Desc   异步请求
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param       $url
 * @param array $post_data
 * @param int   $timeout
 * @return bool
 */
function asyncRequest($url, $post_data = [], $timeout = 30)
{

    ignore_user_abort(TRUE);
    set_time_limit(0);
    $url_arr = parse_url($url);
    $errno   = NULL;
    $errstr  = NULL;
    #$url_arr['port'] = -1;
    $port = isset($url_arr['port']) ? $url_arr['port'] : 80;
    if ($url_arr['scheme'] === 'https') {
        $url_arr['host'] = 'ssl://' . $url_arr['host'];
    }
    $fp = fsockopen($url_arr['host'], $port, $errno, $errstr, $timeout);#打开一个网络连接
    if (!$fp) return FALSE;
    // 转换到非阻塞模式
    stream_set_blocking($fp, 0);
    $getPath = isset($url_arr['path']) ? $url_arr['path'] : '/index.php';
    $getPath .= isset($url_arr['query']) ? '?' . $url_arr['query'] : '';
    $method  = 'GET';  //默认get方式
    if (!empty($post_data)) $method = 'POST';
    $header = '$method  $getPath  HTTP/1.1\r\n';
    $header .= 'Host: ' . $url_arr['host'] . '\r\n';
    if (!empty($post_data)) {  //传递post数据
        /*$_post = [];
        foreach ($post_data as $_k => $_v) {
            $_post[] = $_k . '=' . urlencode($_v);
        }
        $_post    = implode('&', $_post);*/
        $_post    = http_build_query($post_data);
        $post_str = 'Content-Type:application/x-www-form-urlencoded; charset=UTF-8\r\n';
        $post_str .= 'Content-Length: ' . strlen($_post) . '\r\n';  //数据长度
        $post_str .= 'Connection:Close\r\n\r\n';
        $post_str .= $_post;  //传递post数据
        $header   .= $post_str;
    } else {
        $header .= 'Connection:Close\r\n\r\n';
    }
    fwrite($fp, $header);
    usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
    fclose($fp);

    return TRUE;
}

/**
 * @Desc   获取Curl句柄
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param       $url
 * @param array $postData
 * @param array $header
 * @return resource
 */
function getCurlObject($url, $postData = [], $header = [])
{

    $options                         = [];
    $url                             = trim($url);
    $options[CURLOPT_URL]            = $url;
    $options[CURLOPT_TIMEOUT]        = 300;
    $options[CURLOPT_USERAGENT]      = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
    $options[CURLOPT_RETURNTRANSFER] = TRUE;
    foreach ($header as $key => $value) {
        $options[$key] = $value;
    }
    if (!empty($postData) && is_array($postData)) {
        $options[CURLOPT_POST]       = TRUE;
        $options[CURLOPT_POSTFIELDS] = http_build_query($postData);
    }
    if (stripos($url, 'https') === 0) {
        $options[CURLOPT_SSL_VERIFYPEER] = FALSE;
    }
    $ch = curl_init();
    curl_setopt_array($ch, $options);

    return $ch;
}

/**
 * @Desc   Curl并发请求
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param array $chList
 * @return bool
 */
function asyncCurl($chList = [])
{

    // 创建多请求执行对象
    $downloader = curl_multi_init();
    // 将三个待请求对象放入下载器中
    foreach ($chList as $ch) {
        curl_multi_add_handle($downloader, $ch);
    }
    //预定义一个状态变量
    $running = NULL;
    //执行批处理句柄
    do {
        $mrc = curl_multi_exec($downloader, $running);//$running 一个用来判断操作是否仍在执行的标识的引用。
    } while ($mrc == CURLM_CALL_MULTI_PERFORM); //常量 CURLM_CALL_MULTI_PERFORM 代表还有一些刻不容缓的工作要做
    while ($running && $mrc == CURLM_OK) {
        if (curl_multi_select($downloader) != -1) {//curl_multi_select阻塞直到cURL批处理连接中有活动连接,失败时返回-1
            do {
                $mrc = curl_multi_exec($downloader, $running);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }
    //所有请求接收完之后进行数据的解析等后续处理
    //获取http返回的结果
    $true_request = 0;
    foreach ($chList as $key => $ch) {
        //获取内容进行后续处理
        $contents = curl_multi_getcontent($ch);
        $errstr   = curl_error($ch);
        $code     = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        //do something to deal data
        curl_multi_remove_handle($downloader, $chList[$key]);//关闭句柄
        curl_close($ch);
    }

    // 轮询
    /*do {
        while (($execrun = curl_multi_exec($downloader, $running)) == CURLM_CALL_MULTI_PERFORM) ;
        if ($execrun != CURLM_OK) {
            break;
        }

        // 一旦有一个请求完成，找出来，处理,因为curl底层是select，所以最大受限于1024
        while ($done = curl_multi_info_read($downloader)) {
            // 从请求中获取信息、内容、错误
            $info   = curl_getinfo($done['handle']);
            $output = curl_multi_getcontent($done['handle']);
            $error  = curl_error($done['handle']);
            // 将请求结果保存,我这里是打印出来
            #print $output;
            //        print '一个请求下载完成!\n';
            // 把请求已经完成了得 curl handle 删除
            curl_multi_remove_handle($downloader, $done['handle']);
        }
        // 当没有数据的时候进行堵塞，把 CPU 使用权交出来，避免上面 do 死循环空跑数据导致 CPU 100%
        if ($running) {
            $rel = curl_multi_select($downloader, 1);
            if ($rel == -1) {
                usleep(1000);
            }
        }

        if ($running == FALSE) {
            break;
        }
    } while (TRUE);*/

    curl_multi_close($downloader);

    return TRUE;
}

/**
 * @Desc   多进程
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param null $input
 */
function process_execute($input = NULL)
{

    /*$pid = pcntl_fork(); //创建子进程
    if ($pid == 0) {//子进程
        $pid = posix_getpid();
        echo '* Process {$pid} was created, and Executed:\n\n';
        eval($input); //解析命令
        exit;
    } else {//主进程
        $pid = pcntl_wait($status, WUNTRACED); //取得子进程结束状态
        if (pcntl_wifexited($status)) {
            echo '\n\n* Sub process: {$pid} exited with {$status}';
        }
    }*/

    $task            = 0; //任务id
    $taskNum         = 10; //任务总数
    $processNumLimit = 3; //子进程总量限制

    while (TRUE) {
        //产生分支
        $processid = pcntl_fork();

        //创建子进程失败
        if ($processid == -1) {
            echo 'create process error！\n';
            exit(1);
        } //主进程，获得子进程pid
        else if ($processid) {
            $task++; //下一个任务
            $currentProcessid = posix_getpid(); //当前进程的Id
            $parentProcessid  = posix_getppid(); // 父级进程的ID
            $phpProcessid     = getmypid(); //当前php进程的id
            echo 'task:', $task, '\tprocessid:', $processid, '\tcurrentProcessid:', $currentProcessid, '\tparentProcessid:', $parentProcessid, '\tphpProcessid:', $phpProcessid, '\n';
            //控制进程数
            if ($task >= $processNumLimit) {
                echo 'wait chl start！\n';
                $exitid = pcntl_wait($status); //等待退出
                echo 'wait chl end！extid:', $exitid, '\tstatus:', $status, '\n';
            }

            //任务总量控制
            if ($task >= $taskNum) {
                echo 'taskNum enough！\n';
                break;
            }
        } //processid=0为新创建的进程
        else {
            $currentProcessid = posix_getpid(); //当前进程的Id
            $parentProcessid  = posix_getppid(); // 父级进程的ID
            $phpProcessid     = getmypid(); //当前php进程的id
            echo 'task:', $task, '\tprocessid:', $processid, '\tcurrentProcessid:', $currentProcessid, '\tparentProcessid:', $parentProcessid, '\tphpProcessid:', $phpProcessid, '\tbegin!\n';

            echo 'task:', $task, '\tprocessid:', $processid, '\tcurrentProcessid:', $currentProcessid, '\tparentProcessid:', $parentProcessid, '\tphpProcessid:', $phpProcessid, '\tend!\n';

            exit(0); //子进程执行完后退出，防止进入循环创建子进程
        }
    }
}

function async_get_url($url_array, $wait_usec = 0)
{

    if (!is_array($url_array))
        return FALSE;
    $wait_usec = intval($wait_usec);
    $data      = [];
    $handle    = [];
    $running   = 0;
    $mh        = curl_multi_init(); // multi curl handler
    $i         = 0;
    foreach ($url_array as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return don't print
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($ch, CURLOPT_MAXREDIRS, 7);
        curl_multi_add_handle($mh, $ch); // 把 curl resource 放进 multi curl handler 里
        $handle[$i++] = $ch;
    }
    /* 执行 */
    do {
        curl_multi_exec($mh, $running);
        if ($wait_usec > 0) /* 每个 connect 要间隔多久 */
            usleep($wait_usec); // 250000 = 0.25 sec
    } while ($running > 0);
    /* 读取资料 */
    foreach ($handle as $i => $ch) {
        $content  = curl_multi_getcontent($ch);
        $data[$i] = (curl_errno($ch) == 0) ? $content : FALSE;
    }
    /* 移除 handle*/
    foreach ($handle as $ch) {
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);

    return $data;
}
