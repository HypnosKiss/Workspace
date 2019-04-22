<?php
/**
 * CURL rquest
 * User: wwl
 * Date: 2017/10/30
 * Time: 10:44
 */

namespace ApiTool\Controller;

class Http{
    /**
     * @url 字符串，网址
     */
    private $url;
    private $header;
    private $showHeader;
    private $header_size = NULL;

    public function __construct($url){
        $this->url = $url;
    }

    public function setUrl($url){
        $this->header_size = NULL;
        $this->url = $url;
    }

    public function setShowHeader($state = 0){
        $this->showHeader = $state;
    }

    public function setHead($header){
        $this->header = $header;
    }

    public function postRequest($data){
        return $this->curlBase(
            array(
                CURLOPT_POST=>1,
                CURLOPT_POSTFIELDS=>http_build_query($data)
            ));
    }
    //get方式下拿到的cookie
    public function getRequest(){
        return $this->curlBase();
    }

    //post方式下，拿到cookie
    public function getPostCookie($data){
        $output = $this->curlBase(array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ));
        preg_match_all("/set\-cookie:([^\r\n]*)/i", $output, $matches);
        return implode(';', $matches[1]);
    }

    //get方式下拿到的cookie
    public function getGetCookie(){
        $output = $this->curlBase();
        preg_match_all("/set\-cookie:([^\r\n]*)/i", $output, $matches);
        return implode(';', $matches[1]);
    }

    //携带cookie发起post请求
    public function postWithCookie($cookie, $data = array()){
        return $this->curlBase(
            array(
                CURLOPT_COOKIE => $cookie,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => http_build_query($data)
            )
        );
    }

    public function getWithCookie($cookie){
        return $this->curlBase(
            array(
                CURLOPT_COOKIE => $cookie,
            )
        );
    }

    //匹配出所有的cookie
    public function matchCookies($cont){
        preg_match_all("/set\-cookie:([^\r\n]*)/i", $cont, $matches);
        return implode(';', $matches[1]);
    }

    // $optArr, 多个数组
    public function curlBase($optArr= NULL){
        $ch = curl_init();
        if(!empty($this->header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if($this->showHeader){
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        // curl_setopt($ch,CURLOPT_PROXY,'127.0.0.1:8888');
        if(!is_null($optArr)){
            curl_setopt_array($ch, $optArr);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36');
        $output = curl_exec($ch);
        $this->header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        return $output;
    }

    public function getCookieArr($str=''){
        $res = explode(';', trim($str,';'));
        $ck = array();
        if(count($res))foreach ($res as $key => $value) {
            $r = explode('=', trim($value));
            if(count($r)>1)$ck[$r[0]] = $r[1];
        }
        return $ck;
    }

    public function getCookieStr($arr=array(),$key=array()){
        $c = '';
        if(is_array($key)){
            foreach ($arr as $k => $val) {
                if(in_array($k,$key,TRUE)){
                    $c .= $k.'='.$val.';';
                }
            }
        }elseif('ALL'==$key){
            foreach ($arr as $k => $val) {
                $c .= $k.'='.$val.';';
            }
        }
        return $c;
    }

    public function getHeaderSize(){
        if(empty($this->header_size))exit('culr not exec');
        return $this->header_size;
    }

    public function getResponseBody($cont){
        return substr($cont,$this->getHeaderSize());
    }
    public function getResponseHeader($cont){
        return substr($cont,0,$this->getHeaderSize());
    }

    // 把cookie以本文形式存在本地，考虑到有读取两步操作，故不用
    // public function getGetLoginFielCookie(){
    // 	$cookie_jar = tempnam('./', 'cookie_');
    // 	$this->curlBase(
    // 		array(
    // 			CURLOPT_COOKIEJAR => $cookie_jar
    // 			)
    // 		);
    // 	return $cookie_jar;
    // }

    // public function getWithFileCookie($cookie_jar){
    // 	return $this->curlBase(
    // 		array(
    // 			CURLOPT_COOKIEFILE => $cookie_jar
    // 			)
    // 		);
    // }
}