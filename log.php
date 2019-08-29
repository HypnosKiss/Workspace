<?php

class Log
{

    private        $handler  = NULL;

    private        $level    = 15;

    private static $instance = NULL;

    private        $handle   = NULL;

    private static $maxsize  = 1024000;

    public function __construct($file = '')
    {

        $this->handle = fopen($file, 'a');
    }

    public function __destruct()
    {

        fclose($this->handle);
    }

    public static function init($filepath = NULL, $filename = NULL, $level = 15)
    {

        if ($filepath === NULL) {#DOCUMENT_ROOT REQUEST_URI SCRIPT_FILENAME PHP_SELF
            $filepath = dirname($_SERVER['PHP_SELF']);
        }
        if ($filename === NULL) {
            $filename = date('Y-m-d') . '.log';
        }
        $dir = $filepath . '/log';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, TRUE);
        }
        $file = $dir . '/' . $filename;
        if (!self::$instance instanceof self) {
            self::$instance = new self($file);
            self::$instance->setLevel($level);
        }

        return self::$instance;
    }

    private function setLevel($level)
    {

        $this->level = $level;
    }

    public static function DEBUG($msg)
    {

        self::$instance->write(1, $msg);
    }

    public static function WARN($msg)
    {

        self::$instance->write(4, $msg);
    }

    public static function ERROR($msg)
    {

        $debugInfo = debug_backtrace();
        $stack     = "[";
        foreach ($debugInfo as $key => $val) {
            if (array_key_exists("file", $val)) {
                $stack .= ",file:" . $val["file"];
            }
            if (array_key_exists("line", $val)) {
                $stack .= ",line:" . $val["line"];
            }
            if (array_key_exists("function", $val)) {
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        self::$instance->write(8, $stack . $msg);
    }

    public static function INFO($msg)
    {

        self::$instance->write(2, $msg);
    }

    private function getLevelStr($level)
    {

        switch ($level) {
            case 1:
                return 'debug';
                break;
            case 2:
                return 'info';
                break;
            case 4:
                return 'warn';
                break;
            case 8:
                return 'error';
                break;
            default:

        }
    }

    public function write($msg, $level)
    {

        $msg = '[' . date('Y-m-d H:i:s') . '][' . $this->getLevelStr($level) . '] ' . $msg . "\n";
        fwrite($this->handle, $msg, 4096);
    }

    /**
     * @Desc 写入JSON数据   @Editor develop41李翔 2019/4/11 17:13:21
     * @param null $file 文件名
     * @param null $data 需写入数据
     * @return string 返回的json字符串
     */
    public static function writeJson($data = NULL, $file = NULL)
    {

        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        date_default_timezone_set('Asia/Shanghai');
        $files       = explode('/', $file);
        $files_count = count($files);
        $filename    = $files[$files_count - 1];
        $filepath    = substr($file, 0, strripos($file, '/'));
        if ($filepath === NULL) {#DOCUMENT_ROOT REQUEST_URI SCRIPT_FILENAME PHP_SELF
            $filepath = dirname($_SERVER['PHP_SELF']);
        }
        if ($filename === NULL) {
            $filename = date('Y-m-d') . '.log';
        }
        if ($filepath) {
            $dir = $filepath . '/log';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, TRUE);
            }
            $file = $dir . '/' . $filename;
        }

        if (empty($data)) {
            exit(json_encode('空数据'));
        }
        $fileType = pathinfo($file, PATHINFO_EXTENSION);
        if ($file === NULL || $fileType !== 'json') {
            $file = date('Y_m_d_H_i') . '.json';
        }
        //如果日志文件超过了指定大小则备份日志文件
        if (file_exists($filename) && (abs(filesize($filename)) > static::$maxsize)) {
            $newfilename = dirname($filename) . '/' . time() . '-' . basename($filename);
            rename($filename, $newfilename);
        }

        $start = '{';
        $end   = '}';
        $i     = 0;
        if (!file_exists($file)) {#文件不存在
            $dataString = json_encode($data, TRUE);
            $jsonData   = $start . PHP_EOL . '"' . $i . '":' . $dataString . PHP_EOL . $end;
        } else {
            $json      = file_get_contents($file);
            $json      = str_replace(['{', '}', PHP_EOL], ['', ',', ''], $json);
            $arr       = explode(',', $json);
            $count_arr = count($arr);
            $datas     = $arr[$count_arr - 2];
            unset($arr[$count_arr - 1]);
            $json = implode(',' . PHP_EOL, $arr) . ',';
            $i    = str_replace('"', '', explode(':', $datas)[0]);
            ++$i;
            $dataString = $json . PHP_EOL . '"' . $i . '":' . json_encode($data, TRUE);
            $jsonData   = $start . PHP_EOL . $dataString . PHP_EOL . $end;
        }
        file_put_contents($file, $jsonData);
    }

    //写入日志
    public static function writeLog($msg, $file = NULL)
    {

        date_default_timezone_set('Asia/Shanghai');
        $res            = [];
        $res['msg']     = $msg;
        $res['logtime'] = date("Y-m-d H:i:s");
        $files          = explode('/', $file);
        $files_count    = count($files);
        $filename       = $files[$files_count - 1];
        $filepath       = substr($file, 0, strripos($file, '/'));
        if ($filepath === null) {#DOCUMENT_ROOT REQUEST_URI SCRIPT_FILENAME PHP_SELF
            $filepath = dirname($_SERVER['PHP_SELF']);
        }
        if ($filename === null) {
            $filename = date('Y-m-d') . '.log';
        }
        if ($filepath) {
            $dir = $filepath . '/log';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $file = $dir . '/' . $filename;
        }

        if (empty($msg)) {
            exit(json_encode('空数据'));
        }

        if ($file === null) {
            $file = date('Y_m_d_H_i') . '.txt';
        }

        //如果日志文件超过了指定大小则备份日志文件
        if (file_exists($file) && (abs(filesize($file)) > self::$maxsize)) {
            $newfilename = dirname($file) . '/' . time() . '-' . basename($file);
            rename($filename, $newfilename);
        }

        //如果是新建的日志文件,去掉内容中的第一个字符逗号
        if (file_exists($file) && abs(filesize($file)) > 0) {
            $content = "," . json_encode($res);
        } else {
            $content = json_encode($res);
        }

        //往日志文件内容后面追加日志内容
        file_put_contents($file, $content, FILE_APPEND);
    }

    //读取日志
    public static function readLog($filename)
    {

        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $json    = json_decode('[' . $content . ']', TRUE);
        } else {
            $json = '{"msg":"The file does not exist."}';
        }

        return $json;
    }
}
log::writeLog('a1sdas56413asd23');