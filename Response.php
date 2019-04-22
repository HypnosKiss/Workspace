<?php
/**
 * @desc
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * Created by PhpStorm
 * User: develop41
 * Date: 2018/10/13 0013
 * Time: 15:10
 */

class Response
{

    /**
     * @var int $code 返回状态码，其中：200成功，400非法请求，500服务器错误
     */
    protected $code = 200;

    /**
     * @desc   JSON 选项
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @var int
     */
    protected $options = 0;

    /**
     * @var array 待返回给客户端的数据
     */
    protected $data = [];

    /**
     * @var string $msg 错误返回信息
     */
    protected $msg = '';

    /**
     * @var array $headers 响应报文头部
     */
    protected $headers = [];

    /**
     * @var array $debug 调试信息
     */
    protected $debug = [];

    public function __construct()
    {

        $this->addHeaders('Content-Type', 'application/json;charset=utf-8');
        /*$this->setCode($ex->getCode());
        $this->setMsg($ex->getMessage());
        $this->setDebug('exception', $ex->getTrace());*/
        $this->response();
    }

    /**
     * 响应操作
     *
     * 通过工厂方法创建合适的控制器，然后调用指定的方法，最后返回格式化的数据。
     *
     * @return mixed 根据配置的或者手动设置的返回格式，将结果返回
     *  其结果包含以下元素：
     * ```
     *  array(
     *      'ret'   => 200,                //服务器响应状态
     *      'data'  => array(),            //正常并成功响应后，返回给客户端的数据
     *      'msg'   => '',                //错误提示信息
     *  );
     * ```
     */
    public function response()
    {

        $rs = $this;
        try {
            // 接口调度与响应
            $api    = 'Test';#App\Api\Site 命名空间+控制器
            $action = 'index';#index 方法
            $data   = call_user_func([$api, $action]);
            $rs->setData($data);
        } catch (Exception $ex) {
            // 框架或项目可控的异常
            $rs->setCode($ex->getCode());
            $rs->setMsg($ex->getMessage());
        }

        return $rs;
    }
    /** ------------------ setter ------------------ **/

    /**
     * 设置返回状态码
     * @param int $ret 返回状态码，其中：200成功，400非法请求，500服务器错误
     * @return Response
     */
    public function setCode($code)
    {

        $this->code = $code;

        return $this;
    }

    /**
     * 设置返回数据
     * @param array/string $data 待返回给客户端的数据，建议使用数组，方便扩展升级
     * @return Response
     */
    public function setData($data)
    {

        $this->data = $data;

        return $this;
    }

    /**
     * 设置错误信息
     * @param string $msg 错误信息
     * @return Response
     */
    public function setMsg($msg)
    {

        $this->msg = $msg;

        return $this;
    }

    /**
     * @desc   设置 JSON options
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param  $options
     * @return $this
     */
    public function setJsonOption($options)
    {

        $this->options = $options;

        return $this;
    }

    /**
     * 添加报文头部
     * @param string $key     名称
     * @param string $content 内容
     */
    public function addHeaders($key, $content)
    {

        $this->headers[$key] = $content;
    }

    /** ------------------ 结果输出 ------------------ **/

    /**
     * 结果输出
     */
    public function output()
    {

        $this->handleHeaders($this->headers);

        $res = $this->getResult();

        echo $this->formatResult($res);
    }

    /** ------------------ getter ------------------ **/

    /**
     * 根据状态码调整Http响应状态码
     */
    public function adjustHttpStatus()
    {

        $httpStatus = [
            100 => 'Continue(继续)',
            101 => 'Switching Protocols(切换协议)',
            200 => 'OK(成功)',
            201 => 'Created(已创建)',
            202 => 'Accepted(服务器已接受请求，但尚未处理)',
            203 => 'Non-Authoritative Information(未授权信息)',
            204 => 'No Content(无内容)',
            205 => 'Reset Content(重置内容)',
            206 => 'Partial Content(服务器已经成功处理了部分GET请求)',
            207 => 'Multi - Status (多状态)',
            208 => 'Already Reported (已报告)',
            226 => 'IMIM Used (使用的)',
            300 => 'Multiple Choices(多种选择)',
            301 => 'Moved Permanently(永久移动)',
            302 => 'Found(临时移动)',
            303 => 'See Other(查看其他位置)',
            304 => 'Not Modified(未修改)',
            305 => 'Use Proxy(使用代理)',
            306 => 'unused(未使用)',
            307 => 'Temporary Redirect(临时重定向)',
            308 => 'Permanent Redirect(永久重定向)',
            400 => 'Bad Request(错误请求)',
            401 => 'Unauthorized(未授权)',
            402 => 'Payment Required(需要付款)',
            403 => 'Forbidden(禁止访问)',
            404 => 'Not Found(未找到)',
            405 => 'Method Not Allowed(不允许使用该方法)',
            406 => 'Not Acceptable(无法接受)',
            407 => 'Proxy Authentication Required(要求代理身份验证)',
            408 => 'Request Time-out(请求超时)',
            409 => 'Conflict(冲突)',
            410 => 'Gone(已失效)',
            411 => 'Length Required(需要内容长度头)',
            412 => 'Precondition Failed(预处理失败)',
            413 => 'Request Entity Too Large(请求实体过长)',
            414 => 'Request-URI Too Large(请求网址过长)',
            415 => 'Unsupported Media Type(媒体类型不支持)',
            416 => 'Requested range not satisfiable(请求范围不合要求)',
            417 => 'Expectation Failed(预期结果失败)',
            422 => 'Unprocessable Entity (无法处理的实体)',
            429 => 'Too Many Requests (太多的请求)',
            431 => 'Request Header Fields Too Large (请求头字段太大)',
            440 => 'Login Timeout 登录超时',
            449 => 'Retry With 重新发送带',
            451 => 'Redirect 重定向',
            500 => 'Internal Server Error(内部服务器错误)',
            501 => 'Not Implemented(未实现)',
            502 => 'Bad Gateway(网关错误)',
            503 => 'Service Unavailable(服务不可用)',
            504 => 'Gateway Time-out(网关超时)',
            505 => 'HTTP Version not supported(HTTP版本不受支持)',
            507 => 'Insufficient Storage (存储空间不足)',
            508 => 'Loop Detected (检测到循环)',
            510 => 'Not Extended (不延长)',
            511 => 'Network Authentication Required (网络需要身份验证)',

        ];
        $str        = isset($httpStatus[$this->code]) ? $httpStatus[$this->code] : "HTTP/1.1 {$this->code} PhalApi Unknown Status";
        @header($str);

        return $this;
    }

    public function getResult()
    {

        $res = [
            'ret'  => $this->code,
            'data' => $this->data,
            'msg'  => $this->msg,
        ];

        if (!empty($this->debug)) {
            $res['debug'] = $this->debug;
        }

        return $res;
    }

    /** ------------------ 内部方法 ------------------ **/

    protected function handleHeaders($headers)
    {

        foreach ($headers as $key => $content) {
            @header($key . ': ' . $content);
        }
    }

    /**
     * @desc   格式化需要输出返回的结果
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param  array $result 待返回的结果数据
     * @return false|string
     */
    protected function formatResult($result)
    {

        return json_encode($result, $this->options);
    }

}