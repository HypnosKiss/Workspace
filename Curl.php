<?php

/**
 * @desc   CURL请求类
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @Class  Curl
 */
class Curl
{

    /**
     * 最大重试次数
     */
    const MAX_RETRY_TIMES = 10;

    /**
     * @var int $retryTimes 超时重试次数；注意，此为失败重试的次数，即：总次数 = 1 + 重试次数
     */
    protected $retryTimes;

    protected $header    = [];

    protected $option    = [];

    protected $hascookie = FALSE;

    protected $cookie    = [];

    /**
     * Curl constructor.
     * @param int $retryTimes 超时重试次数，默认为1
     */
    public function __construct($retryTimes = 1)
    {

        $this->retryTimes = $retryTimes < static::MAX_RETRY_TIMES ? $retryTimes : static::MAX_RETRY_TIMES;
    }

    /**
     * @desc   GET方式的请求
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param  string $url       请求的链接
     * @param  int    $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     * @throws \Exception
     */
    public function get($url, $timeoutMs = 3000)
    {

        return self::request($url, [], $timeoutMs);
    }

    /**
     * @desc   POST方式的请求
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param  string $url       请求的链接
     * @param  array  $data      POST的数据
     * @param  int    $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     * @throws \Exception
     */
    public static function post($url, $data, $timeoutMs = 3000)
    {

        return self::request($url, $data, $timeoutMs);
    }

    /**
     * @desc   统一接口请求
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param string $url       请求的链接
     * @param array  $data      POST的数据
     * @param   bool $ssl       是否为https
     * @param int    $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     * @return bool
     */
    protected static function request($url, $data, $ssl, $timeoutMs = 3000)
    {

        $self = new self();
        // curl初始化
        $curl = curl_init();
        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);//referer头，请求来源
        #curl_setopt($curl, CURLOPT_TIMEOUT_MS, 30);//设置超时时间
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);//设置超时时间
        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        if (!empty($data)) {
            // 处理post相关选项
            curl_setopt($curl, CURLOPT_POST, TRUE);// 是否为POST请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 处理请求数据
        }
        curl_setopt($curl, CURLOPT_HEADER, FALSE);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);//curl_exec()是否返回响应结果
        // 发出请求
        /*$response = curl_exec($curl);
        if (FALSE === $response) {
            echo '<br>', curl_error($curl), '<br>';

            return FALSE;
        }*/

        $curRetryTimes = $self->retryTimes;
        do {
            $response = curl_exec($curl);
            $curRetryTimes--;
        } while ($response === FALSE && $curRetryTimes >= 0);
        $errno = curl_errno($curl);
        if ($errno) {
            echo '<br>', $errno, '<br>';

            return FALSE;
        }

        curl_close($curl);

        return $response;
    }

    /**
     * @param   string $url 网址
     * @param   bool   $ssl 是否为https
     * @return  bool|mixed  返回数据
     */
    public static function curlGet($url, $ssl = TRUE)
    {

        // curl完成
        $curl = curl_init();
        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);//referer头，请求来源
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间
        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        curl_setopt($curl, CURLOPT_HEADER, FALSE);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);//curl_exec()是否返回响应结果

        // 发出请求
        $response = curl_exec($curl);
        if (FALSE === $response) {
            echo '<br>', curl_error($curl), '<br>';

            return FALSE;
        }
        curl_close($curl);

        return $response;
    }

    /**
     * @param   string $url  网址
     * @param   array  $data 数据
     * @param   bool   $ssl  是否为https
     * @return  bool|mixed 返回数据
     */
    public static function curlPost($url, $data, $ssl = TRUE)
    {

        // curl完成
        $curl = curl_init();
        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);//referer头，请求来源
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间
        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        // 处理post相关选项
        curl_setopt($curl, CURLOPT_POST, TRUE);// 是否为POST请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 处理请求数据
        // 处理响应结果
        curl_setopt($curl, CURLOPT_HEADER, FALSE);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);//curl_exec()是否返回响应结果

        // 发出请求
        $response = curl_exec($curl);
        if (FALSE === $response) {
            echo '<br>', curl_error($curl), '<br>';

            return FALSE;
        }
        curl_close($curl);

        return $response;
    }

}