<?php
/**
 * @Desc
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * Created by PhpStorm
 * User: develop41
 * Date: 2018-12-05 17:41:05
 */

class Tool
{

    /**
     * 测试数据打印
     * */
    public static function p()
    {

        $args   = func_get_args();
        $output = '';
        foreach ($args as $arg) {
            @$output .= var_export($arg, TRUE);
        }
        echo '<pre>' . $output . '</pre>';
        exit();
    }

    /**
     * @ideas json格式返回
     * @param array  $data 数据
     * @param int    $code 状态码
     * @param string $msg  提示信息
     */
    public static function jsonReturn($data = NULL, $code = 200, $msg = '操作成功')
    {

        @header('Content-Type:application/json;charset=utf-8');
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
        if ($code !== 200 && $msg === '成功') {
            $msg = '操作失败';
        }
        if (is_numeric($data)) {
            $tmp  = $data;
            $data = $msg === '操作成功' ? NULL : $msg;
            $msg  = $code === 200 ? '操作失败' : $code;
            $code = $tmp;
        }
        if (is_string($data)) {
            $tmp  = $data;
            $data = $msg === '操作成功' ? NULL : $msg;
            $msg  = $tmp;
        }
        $param = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        echo json_encode($param, TRUE);
        exit;
    }

    /**
     * @Desc   生成树状图
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param array  $arr      要处理的数据
     * @param int    $parentid 父级id
     * @param int    $level    等级
     * @param string $pidname  记录父级id的字段
     * @param string $name     分类字段名
     * @param string $icon     子级标识符
     * @param string $pk       主键
     * @return array
     */
    public static function getTree(array $array, $parentid = 0, $level = 0, $pidname = 'pid', $name = 'name', $icon = '┗━', $pk = 'id')
    {

        $arr = [];//初始化
        foreach ($array as $k => $v) {
            if ((int)$v[$pidname] === (int)$parentid) {
                $flg      = str_repeat($icon, $level);//把字符串 "┗━ " 重复 $level次：
                $v[$name] = $flg . $v[$name];//把子级名字组合为新的名字
                $arr[]    = $v;
                $arr      = array_merge($arr, self::getTree($array, $v[$pk], $level + 1, $pidname, $name));
            }
        }

        return $arr;
    }

    /**
     * @Desc   判断是否为时间戳
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $timestamp
     * @return bool
     */
    public static function isTimeStamp($timestamp)
    {

        $date = date('Y-m-d', $timestamp);
        if (strtotime(date('Y-m-d H:i:s', $timestamp)) === (int)$timestamp && $date !== '1970-01-01') {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * @Desc   格式化时间戳
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param array  $arr    需要格式化的数组
     * @param string $format 时间格式默认Y-m-d H:i:s
     */
    public static function formatTime(&$arr, $format = 'Y-m-d H:i:s')
    {

        array_walk_recursive($arr, function (&$v) use ($format) {

            $isTimeStamp = self::isTimeStamp($v);
            if ($isTimeStamp === TRUE) {
                $v = date($format, $v);
            }
        });
    }

    /**
     * @Desc   数组均分
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param array $arr 拆分数组
     * @param int   $num 拆分个数
     * @return array
     */
    public static function arrSplit(&$arr, $num)
    {

        $arrCopy = $arr;
        $arr     = [];
        array_walk($arrCopy, function (&$value, $key, $num) use (&$arr) {

            $index = $key / $num;
            if ($key < $num) {#判断$key 是否大于拆分个数
                $arr[$key][] = $value;
            } else if ($index >= 1) {# $index>=1 则大于拆分个数 循环重置索引插入数组
                $keys      = explode('.', $index);
                $i         = $key - $keys[0] * $num;
                $arr[$i][] = $value;
                ++$i;
            }
        }, $num);

        return $arr;
    }

    /**
     * @desc数组过滤空格
     * @param        $arr
     * @param string $charlist
     * @return mixed
     */
    public static function arrayTrim(&$arr, $charlist = ' ')
    {

        array_walk_recursive($arr, function (&$v) use ($charlist) {

            $v = trim($v, $charlist);
        });
    }

    /**
     * @desc 删除空数组
     * @param        $arr
     * @param array  $charList
     * @return mixed
     */
    public static function unsetEmptyArray(&$arr, $charList = ['', 0, 0.0, '0', NULL, FALSE, []])
    {

        if (is_array($arr)) {
            $arrCopy = $arr;
            $arr     = [];
            array_walk_recursive($arrCopy, function ($v, $k) use ($charList) {

                if (!in_array($v, $charList, TRUE)) {
                    $arr[$k] = $v;
                }
            }, $arr);
        }

        return FALSE;
    }

    /**
     * 文章访问日志
     * 下载的日志文件通常很大, 所以先设置csv相关的Header头, 然后打开
     * PHP output流, 渐进式的往output流中写入数据, 写到一定量后将系统缓冲冲刷到响应中
     * 避免缓冲溢出
     */
    public static function ExcelImport($timeStart, $timeEnd)
    {

        /**
         * $fp = fopen('php://output', 'a');
         * fputs($fp, 'strings');
         * fclose($fp)
         */
        set_time_limit(0);
        $columns = [
            '文章ID', '文章标题',
        ];
        #$csvFileName = '用户日志' . $timeStart . '_' . $timeEnd . '.xlsx';
        $fileName = '用户日志' . $timeStart . '_' . $timeEnd . '.xlsx';
        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流 a写入方式打开
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
        //刷新输出缓冲到浏览器
        ob_flush();
        flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
        fclose($fp);
        exit();
    }

    public static function isPWD($value, $minLen = 5, $maxLen = 16)
    {

        $match = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{' . $minLen . ',' . $maxLen . '}$/';
        $v     = trim($value);
        if (emptyempty($v))
            return FALSE;

        return preg_match($match, $v);
    }

    /**
     * @Author        :   HTL
     * @Description   : 移出单元列
     * @objPHPExcel   : phpexecel object
     * @remove_columns:要移出的列
     */
    public static function remove_column($objPHPExcel, $remove_columns)
    {

        if (!$objPHPExcel
            || !is_object($objPHPExcel)
            || !$remove_columns
            || !is_array($remove_columns)
            || count($remove_columns) <= 0) return;
        //单元格模板值,用于匹配要删除的列(在excel模板第一列)
        $cell_val = '';
        //单元格总列数
        $highestColumm = $objPHPExcel->getActiveSheet()->getHighestColumn();
        for ($column = 'A'; $column <= $highestColumm;) {
            //列数是以A列开始
            $cell_val = $objPHPExcel->getActiveSheet()->getCell($column . "1");
            $cell_val = preg_replace("/[\s{}]/i", "", $cell_val);
            //移出没有权限导出的列
            //移出后column不能加1,因为当前列已经移出加1后会导致删除错误的列
            //此问题浪费了几十分钟
            if (strlen($cell_val) > 0 && in_array($cell_val, $remove_columns)) {
                $objPHPExcel->getActiveSheet()->removeColumn($column);
            } else {
                $column++;
            }
        }
    }

    /**
     * @Desc   Excel导出
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function Excel()
    {

        $zhuanrang = M('zhuanrang');//转让
        if ($_FILES['excel']['error'][0] == 0) {
            $time             = $time = times(1);
            $upload           = new \Think\Upload();// 实例化上传类
            $upload->maxSize  = 10485760;// 设置附件上传大小
            $upload->exts     = ['xls', 'xlsx'];// 设置附件上传类型
            $upload->rootPath = './Public/Excel/';//上传根目录
            $upload->autoSub  = FALSE;// 将自动生成以photo后面加时间的形式文件夹，关闭
            $info             = $upload->upload();// 上传文件
            $exts             = $info['excel']['ext'];//文件后缀
            $filename         = $upload->rootPath . $info['excel']['savename'];// 生成文件路径名
            if (!$info) {
                #$this->error($upload->getError());// 上传错误提示错误信息
            } else {
                vendor("PHPExcel.PHPExcel");// 导入PHPExcel类库
                $PHPExcel  = new \PHPExcel();// 创建PHPExcel对象，注意，不能少了
                $city      = mb_substr($info['excel']['name'], 0, 2, 'utf8') . '市';//中文无乱码截取字符串拼接城市
                $district  = M('district');//城市表
                $citylist  = $district->field('id')->where("name = '$city'")->find();//查找当前城市信息
                $adminlist = M('zrservice')->field('id,display_times,home_times')->where('uid = 1 AND state = 0')->select();//查出uid为1的用户所有套餐
                if ($exts == 'xls') {// 如果excel文件后缀名为.xls，导入这个类
                    vendor("PHPExcel.PHPExcel.Reader.Excel5");
                    $PHPReader = new \PHPExcel_Reader_Excel5();
                } else if ($exts == 'xlsx') {
                    vendor("PHPExcel.PHPExcel.Reader.Excel2007");
                    $PHPReader = new \PHPExcel_Reader_Excel2007();
                }
                $PHPExcel     = $PHPReader->load($filename);
                $currentSheet = $PHPExcel->getSheet(0);// 获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
                $allColumn    = $currentSheet->getHighestColumn();// 获取总列数
                $allRow       = $currentSheet->getHighestRow();// 获取总行数
                if (count($adminlist) < $allRow) {
                    #$this->error('套餐不足,请使用15679288058账号购买套餐');
                }
                $data = [];
                for ($j = 1; $j <= $allRow; $j++) {
                    //从A列读取数据
                    for ($k = 'A'; $k <= $allColumn; $k++) {
                        // 读取单元格
                        $data[$j][] = $PHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
                    }
                }
                for ($i = 2; $i <= $allRow; $i++) {
                    $data_p['title']    = $PHPExcel->getActiveSheet()->getCell("A" . $i)->getValue() == '' ? '转让' : $PHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();//标题
                    $data_p['district'] = $PHPExcel->getActiveSheet()->getCell("B" . $i)->getValue() == '' ? '深圳' : $PHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();//详细地区
                    $data_p['users']    = $PHPExcel->getActiveSheet()->getCell("C" . $i)->getValue() == '' ? '李生' : $PHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();//联系人
                    $data_p['phone']    = $PHPExcel->getActiveSheet()->getCell("D" . $i)->getValue() == '' ? 0773 - 23212184 : $PHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();//手机
                    $data_p['rent']     = $PHPExcel->getActiveSheet()->getCell("E" . $i)->getValue() == '' ? rand(3000, 100000) : $PHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();//租金
                    $data_p['moneys']   = $PHPExcel->getActiveSheet()->getCell("F" . $i)->getValue() == '' ? rand(30, 500) : $PHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();//转让费
                    $data_p['area']     = $PHPExcel->getActiveSheet()->getCell("G" . $i)->getValue() == '' ? rand(30, 999) : $PHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();//面积
                    $data_p['city']     = $citylist['id'];//当前城市id
                    $dityourone         = mb_substr($data_p['district'], 0, 2, 'utf8');//无乱码截取地区名字
                    //拼接条件查询当前城市地区信息
                    $dityour = $district->field('id,name')->where('upid = ' . $citylist['id'])->select();//查出当前城市地区信息
                    foreach ($dityour as $value) {
                        if ($dityourone == mb_substr($value['name'], 0, 2, 'utf8')) {
                            $search = $value['id'];//当前地区id
                        }
                    }
                    $data_p['dityour'] = $search == '' ? $value['id'] : $search;
                    $keyword           = $city . $data_p['district'];//拼接请求坐标信息地址
                    //高德逆地理坐标地址
                    $url           = "https://restapi.amap.com/v3/geocode/geo?key=c843154067adddf6666589e63c94bd85&address={$keyword}";
                    $coordinateone = curlGet($url);//得到地区逆地理编码信息
                    $coordinate    = json_decode($coordinateone, TRUE);//解码
                    //得到坐标
                    $data_p['coordinate'] = str_replace(',', '##', $coordinate['geocodes'][0]['location']) == '' ? '113.868829##22.582422' : str_replace(',', '##', $coordinate['geocodes'][0]['location']);
                    $data_p['facilty']    = '1%%1%%1%%1%%1%%1%%1%%1%%1%%1';
                    $data_p['suit']       = '202' . rand(0, 9) . '-' . rand(1, 12) . '-01';
                    $data_p['type']       = rand(1, 12);
                    $data_p['descript']   = '转让';
                    $data_p['publisher']  = 15679288058;
                    $data_p['images']     = 'Uploads/2017-09-08/20171214123705.png';
                    $data_p['views']      = rand(15, 999);
                    $data_p['time']       = $time;
                    $data_p['shenhe']     = 1;//审核状态
                    $id[]                 = $zhuanrang->add($data_p);//插入数据
                }
                if ($id) {
                    $num = count($id);
                    for ($a = 0; $a < $num; $a++) {
                        $zrservicelist['shopid'] = $id[$a];
                        $zrservicelist['state']  = 1;
                        $zrid                    = $adminlist[$a]['id'];
                        $home_times              = $adminlist[$a]['home_times'];
                        $display_times           = $adminlist[$a]['display_times'];
                        if ($home_times > 0) {
                            $zrservicelist['home_time'] = $time;// 通过审核开始时间为当前时间
                            // 通过审核结束时间为当前时间+套餐天数
                            $zrservicelist['home_timeed'] = date("Y-m-d", strtotime("+{$home_times} days", strtotime("{$time}")));
                        }
                        //+++++ 地图推荐套餐时间大于0 计算出开始时间和结束时间 +++++
                        if ($display_times > 0) {
                            $zrservicelist['map_time'] = $time;// 通过审核开始时间为当前时间
                            // 通过审核结束时间为当前时间+套餐天数
                            $zrservicelist['display_timeed'] = date("Y-m-d", strtotime("+{$display_times} days", strtotime("{$time}")));
                        }
                        $zrse[] = M('zrservice')->where('id=' . $zrid)->save($zrservicelist);
                    }
                    if ($zrse) {
                        #$this->success("导入数据成功,已自动选择套餐");
                    } else {
                        #$this->error('数据导入成功,套餐未选择,请到详情修改套餐');
                    }
                } else {
                    #$this->error("导入失败，原因可能是excel表中格式错误", "5");// 提示错误
                }
            }
        } else {
            #$this->error('没有文件上传', '', 1);
        }
    }

    /**
     * @Desc   多维数组排序
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param     $data
     * @param     $sort_order_field
     * @param int $sort_order
     * @param int $sort_type
     * @return mixed
     */
    public static function my_array_multisort($data, $sort_order_field, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
    {

        foreach ($data as $val) {
            $key_arrays[] = $val[$sort_order_field];
        }
        array_multisort($key_arrays, $sort_order, $sort_type, $data);

        return $data;
    }

    /**
     * 格式输出字符串
     * */
    public static function strFormat()
    {

        $args   = func_get_args();
        $format = array_shift($args);

        preg_match_all('/(?=\{)\{(\d+)\}(?!\})/', $format, $matches, PREG_OFFSET_CAPTURE);
        $offset = 0;
        foreach ($matches[1] as $data) {
            $i      = $data[0];
            $format = substr_replace($format, @$args[$i], $offset + $data[1] - 1, 2 + strlen($i));
            $offset += strlen(@$args[$i]) - 2 - strlen($i);
        }

        return $format;
    }

    /**
     * 返回图标字符串
     * @param $icon string
     * @return string
     */
    public static function stringIcon($icon)
    {

        return self::strFormat('<span class="icon {0}" style="height: 24px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>', $icon);
    }

    /**
     * 返回指定时间是星期几
     * @param $date
     * @return mixed
     */
    public static function getWeek($date = '')
    {

        if ($date == '') {
            $date = date('Y-m-d');
        }
        //强制转换日期格式
        $date_str = date('Y-m-d', strtotime($date));
        //封装成数组
        $arr = explode("-", $date_str);
        //参数赋值
        //年
        $year = $arr[0];
        //月，输出2位整型，不够2位右对齐
        $month = sprintf('%02d', $arr[1]);
        //日，输出2位整型，不够2位右对齐
        $day = sprintf('%02d', $arr[2]);
        //时分秒默认赋值为0；
        $hour = $minute = $second = 0;
        //转换成时间戳
        $strap = mktime($hour, $minute, $second, $month, $day, $year);
        //获取数字型星期几
        $number_wk = date("w", $strap);
        //自定义星期数组
        $weekArr = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];

        //获取数字对应的星期
        return $weekArr[$number_wk];
    }

    /**
     * @Desc   获取指定日期的开始时间和结束时间
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param null $date        默认今天的日期 2018-12-12
     * @param bool $isTimestamp 默认返回格式为时间戳
     * @return array
     */
    public static function getToday($date = NULL, $isTimestamp = TRUE)
    {

        if ($date === NULL) {
            $date = date('Y-m-d');
        }
        $start_time = date('Y-m-d', strtotime('-1 day', $date)) . ' 23:59:59';
        $end_time   = date('Y-m-d', strtotime('+1 day', $date)) . ' 00:00:00';
        if ($isTimestamp === TRUE) {
            $start_time = strtotime($start_time);
            $end_time   = strtotime($end_time);
        }
        $response = [
            'start_time' => $start_time,
            'end_time'   => $end_time,
        ];

        return $response;
    }

    /**
     * 获取指定日期周一
     * @param int  $timestamp
     * @param bool $is_return_timestamp
     * @return mixed
     */
    public static function thisMonday($timestamp = 0, $is_return_timestamp = TRUE)
    {

        static $cache;
        $id = $timestamp . $is_return_timestamp;
        if (!isset($cache[$id])) {
            if (!$timestamp) $timestamp = time();
            $time        = $timestamp - 24 * 3600 * date('w', $timestamp) + (date('w', $timestamp) > 0 ? 86400 : -6 * 86400);
            $monday_date = date('Y-m-d', $time);
            if ($is_return_timestamp) {
                $cache[$id] = strtotime($monday_date);
            } else {
                $cache[$id] = $monday_date;
            }
        }

        return $cache[$id];
    }

    /**
     * 获取指定日期周五
     * @param int  $timestamp
     * @param bool $is_return_timestamp
     * @return mixed
     */
    public static function thisFriday($timestamp = 0, $is_return_timestamp = TRUE)
    {

        static $cache;
        $id = $timestamp . $is_return_timestamp;
        if (!isset($cache[$id])) {
            if (!$timestamp) $timestamp = time();
            $friday = self::thisMonday($timestamp) + 4 * 86400;
            if ($is_return_timestamp) {
                $cache[$id] = $friday;
            } else {
                $cache[$id] = date('Y-m-d', $friday);
            }
        }

        return $cache[$id];
    }

    /**
     * 获取指定日期周日
     * @param int  $timestamp
     * @param bool $is_return_timestamp
     * @return mixed
     */
    public static function thisSunday($timestamp = 0, $is_return_timestamp = TRUE)
    {

        static $cache;
        $id = $timestamp . $is_return_timestamp;
        if (!isset($cache[$id])) {
            if (!$timestamp) $timestamp = time();
            $friday = self::thisMonday($timestamp) + 6 * 86400;
            if ($is_return_timestamp) {
                $cache[$id] = $friday;
            } else {
                $cache[$id] = date('Y-m-d', $friday);
            }
        }

        return $cache[$id];
    }

    /**
     * @Desc   把星期转换为时间格式
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param string $week
     * @param bool   $is_return_timestamp
     * @return mixed
     */
    public static function weekConversionTime($week = '星期一', $is_return_timestamp = TRUE)
    {

        static $cache;
        static $weekArr = ["星期日" => 0, "星期天" => 0, "星期一" => 1, "星期二" => 2, "星期三" => 3, "星期四" => 4, "星期五" => 5, "星期六" => 6];
        $id = $week . $is_return_timestamp;
        if (!isset($cache[$id])) {
            if (!$week) {
                $week = date('w');
            }
            $sunday = self::thisSunday();
            $today  = self::getWeek();
            $day    = $weekArr[$week];
            $today  = $weekArr[$today];
            $sign   = '+';
            if ($day <= $today) {
                $sign = '-';
                if (!$day) {#星期天
                    $day = $today - 6;
                } else {
                    $day = $today - $day + 1;
                }
            }
            $timestamp_ = strtotime($sign . $day . ' day', $sunday);
            if ($is_return_timestamp) {
                $cache[$id] = $timestamp_;
            } else {
                $cache[$id] = date('Y-m-d', $timestamp_);
            }
        }

        return $cache[$id];
    }

    /**
     * 格式化浮点数，精确一位小数点，没有就补0
     * @param     $value
     * @param int $is_percent
     * @return float|string
     */
    public static function formatFloat($value, $is_percent = 0)
    {

        $value = floatval($value);
        if ($is_percent > 0) {
            $value = $value * 100;
        }
        $str = strval($value);
        if (strpos($str, '.') !== FALSE) {
            return round($value, 1);
        } else {
            return $str . ".0";
        }
    }

    /**
     * 鼠标移上去提示隐藏内容
     * @param $value
     * @return string
     */
    public static function tipShow($value)
    {

        return '<span title="' . $value . '">' . $value . '</span>';
    }

    /**
     * @desc连接redis
     * @return \Redis
     */
    public static function redis()
    {

        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);

        return $redis;
    }

    /**
     * @desc计算时间差:默认返回类型为：分钟
     * @param string $old_time    只能是时间戳 ,
     * @param string $return_type 为 h 是小时,为 s 是秒
     * @return  float
     */
    public static function timecount($old_time, $return_type = 'm')
    {

        if ($old_time < 1) {
            echo '无效的Unix时间戳';
            die;
        } else {
            switch ($return_type) {
                case 'h':
                    $type = 3600;
                    break;
                case 'm':
                    $type = 60;
                    break;
                case 's':
                    $type = 1;
                    break;
                default:
                    $type = 60;
                    break;
            }

            return round((time() - $old_time) / $type);
        }
    }

    /**
     * @ideas 接口限流
     * @param string $usb    接口名
     * @param int    $count  限流次数
     * @param int    $timeed key过期时间单位(s）
     * @param string $type   计算时间时间类型( h ,m ,s)
     * @param int    $temps  用于判断最后一次访问接口时间是否小于这个时间
     * @return int   返回队列长度
     */
    public static function interfaceCurrentLimiting($usb = NULL, $count = 10, $type = 'm')
    {

        switch ($type) {
            case 'd':#天
                $expireTime = 86400;
                break;
            case 'h':#时
                $expireTime = 3600;
                break;
            case 'm':#分
                $expireTime = 60;
                break;
            case 's':
                $expireTime = 1;
                break;
            default:
                $expireTime = 60;
                break;
        }
        $ip    = get_client_ip();//获取ip地址
        $redis = redis();//连接redis
        $len   = $redis->lLen($ip . $usb);//队列长度
        if ($len < $count) {//调用小于调用次数
            $redis->lPush($ip . $usb, $_SERVER['REQUEST_TIME']);//入队
            $redis->expire($ip . $usb, $expireTime);//用于设置 key 的过期时间。key 过期后将不再可用。
        } else {//调用大于调用次数
            $last_time = $redis->lIndex($ip . $usb, 0);//最后一次调用时间
            $time      = $_SERVER['REQUEST_TIME'] - $last_time;//返回最后一次调用时间离现在多少s
            if ($time < $expireTime) {//小于过期时间
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * @Desc   图片上传
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param null  $file_name 文件名
     * @param null  $dir       目录 '/Public/express'
     * @param array $params
     * @return array
     */
    public static function uploadImg($file_name = NULL, $dir = NULL, $params = NULL)
    {

        $dir = '/Uploads/' . $dir;
        //创建资料目录
        #$save_paths  = dirname(__DIR__, 3) . '/Uploads/' . $dir;#图片目录  "/Uploads/userinfo/1/images",
        $save_path = dirname(dirname(dirname(__DIR__))) . $dir;#图片目录   "/alidata/www/xcapp/Uploads/userinfo/1/images"
        $res       = self::createDir($save_path);
        if (!$res) {
            show_msg_json(-4, '上传目录创建失败');
        }
        $byte                 = 1048576;# 1 M 代表的字节数
        $min_num              = $params['min_num'] > 0 ? $params['min_num'] : 1;#默认最少1张
        $max_num              = $params['max_num'] > 0 ? $params['min_num'] : 9;#默认最多9张
        $min_upload_field_num = $params['min_upload_field_num'] > 0 ? $params['min_upload_field_num'] : 1;
        $max_upload_field_num = $params['max_upload_field_num'] > 0 ? $params['max_upload_field_num'] : 9;
        $size                 = $params['size'] > 0 ? $params['size'] : 10;#默认100M
        $size                 *= $byte;
        $ext                  = !empty($params['exts']) ? $params['exts'] : ['jpg', 'gif', 'png', 'jpeg'];
        $upload_field_num     = count($_FILES);#上传文件字段数
        if ($upload_field_num < $min_upload_field_num) {
            jsonReturn([], '-19', '上传图片字段不能少于' . $min_num . '个');
        }
        if ($upload_field_num > $max_upload_field_num) {
            jsonReturn([], '-15', '上传图片字段数量大于' . $max_num . '个');
        }
        static $count = 0;
        array_map(function ($v) use (&$count) {

            $count = count($v['name']) + $count;
        }, $_FILES);
        if ($count < $min_num) {
            jsonReturn([], '-19', '上传的图片总数少于' . $min_num . '张');
        }
        if ($count > $max_num) {
            jsonReturn([], '-15', '上传的图片总数大于' . $max_num . '张');
        }
        if (is_array($_FILES) && $count) {
            if ($file_name === NULL) {
                foreach ($_FILES as $FILE) {
                    $file_name = date('YmdHis') . '_' . $FILE['size'];
                }
            }
            //上传图片
            $upload           = new \Think\Upload();// 实例化上传类
            $upload->maxSize  = $size;// 设置附件上传大小 100M
            $upload->exts     = $ext;// 设置附件上传类型
            $upload->rootPath = $save_path . '/'; // 设置附件上传根目录
            $upload->saveName = ['uniqid', $file_name . '_']; //订单号命名
            $upload->replace  = TRUE; //覆盖上传
            $info             = $upload->upload();
            if (is_array($info)) {
                $file = [];
                foreach ($info as $value) {
                    if ($value['savename'] !== '') {
                        $file [] = $dir . '/' . $value['savepath'] . $value['savename'];
                    } else {
                        show_msg_json('-3', '上传失败.', ['errormsg' => $upload->getError()]);
                    }
                }

                return $file;
            }
            show_msg_json('-3', '上传失败.', ['error_msg' => $upload->getError()]);
        }
        show_msg_json('-7', '没有图片上传');
    }

    /**
     * @Desc   创建目录
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param string $save_path 目录路径
     * @return bool
     */
    public static function createDir($save_path)
    {

        if (!is_dir($save_path)) {
            return mkdir($save_path, 0777, TRUE);
        }

        return TRUE;
    }

    /**
     * @Desc   递归创建目录
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $path
     * @return bool
     */
    public static function mkdir($path)
    {

        // 判断传过来的$path是否已是目录，若是，则直接返回true
        if (is_dir($path)) {
            return TRUE;
        }
        // 走到这步，说明传过来的$path不是目录
        // 判断其上级是否为目录，是，则直接创建$path目录
        if (is_dir(dirname($path))) {
            return mkdir($path);
        }
        // 走到这说明其上级目录也不是目录,则继续判断其上上...级目录
        self::createDir(dirname($path));

        // 走到这步，说明上级目录已创建成功，则直接接着创建当前目录，并把创建的结果返回
        return mkdir($path);
    }

    /**
     * @Desc   递归根据特定key对数组排序
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param        $data
     * @param string $orderKey
     * @param string $sonKey
     * @param int    $orderBy
     * @return mixed
     */
    public static function recursion_order_by($data, $orderKey = 'order', $sonKey = 'children', $orderBy = SORT_ASC)
    {

        $func = function ($value) use ($sonKey, $orderKey, $orderBy) {

            if (isset($value[$sonKey]) && is_array($value[$sonKey])) {
                $value[$sonKey] = self::recursion_order_by($value[$sonKey], $orderKey, $sonKey, $orderBy);
            }

            return $value;
        };

        return self::recursion_order_by(array_map($func, $data), $orderKey, $orderBy);
    }

    /**
     * @Desc   生成分类树
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param array  $array
     * @param int    $parentId
     * @param string $pidName
     * @param string $childName
     * @param string $pk
     * @return array
     */
    public static function getChildTree(array $array, $parentId = 0, $pidName = 'pid', $childName = 'child', $pk = 'id')
    {

        $arr = [];//初始化
        foreach ($array as $key => $item) {
            if ((int)$item[$pidName] === (int)$parentId) {
                $item[$childName] = self::getChildTree($array, $item[$pk]);
                $arr[]            = $item;
            }
        }

        return $arr;
    }

    /**
     * @Desc   返回本月的开始时间和结束时间
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param int  $timestamp
     * @param bool $is_return_timestamp
     * @return mixed
     */
    public static function thisMonthStartTimeAndEndTime($timestamp = 0, $is_return_timestamp = TRUE)
    {

        $isTimeStamp = self::isTimeStamp($timestamp);
        if ($isTimeStamp === FALSE) {
            $timestamp = strtotime($timestamp);
        }
        $startTime = date('Y-m-d', strtotime('last day of -1 month', $timestamp));#上个月的最后一天
        $startTime = strtotime('+1 day', strtotime($startTime));# 本月的第一天 =上个月的最后一天+1天
        $endTime   = date('Y-m-d', strtotime('first day of +1 month', $timestamp));#下个月的第一天
        $endTime   = strtotime('-1 day', strtotime($endTime));# 本月的第一天 =下个月的第一天-1天
        if ($is_return_timestamp === FALSE) {
            $startTime = date('Y-m-d H:i:s', $startTime);
            $endTime   = date('Y-m-d H:i:s', $endTime);
        }

        return [
            'startTime' => $startTime,
            'endTime'   => $endTime,
        ];
    }

    public static function thisWeekStartTimeAndEndTime($timestamp = 0, $is_return_timestamp = TRUE)
    {

        $isTimeStamp = self::isTimeStamp($timestamp);
        if ($isTimeStamp === FALSE) {
            $timestamp = strtotime($timestamp);
        }
        $startTime = self::thisMonday($timestamp);
        $endTime   = \strtotime('+1 day', self::thisSunday($timestamp));
        if ($is_return_timestamp === FALSE) {
            $startTime = date('Y-m-d H:i:s', $startTime);
            $endTime   = date('Y-m-d H:i:s', $endTime);
        }

        return [
            'startTime' => $startTime,
            'endTime'   => $endTime,
        ];
    }

    /**
     * @Desc 写入JSON数据   @Editor develop41李翔 2019/4/11 17:13:21
     * @param null $file 文件名
     * @param null $data 需写入数据
     * @return string 返回的json字符串
     */
    public static function writeJson($data = NULL, $file = NULL)
    {

        if (empty($data)) {
            return '空数据';
        }
        $fileType = pathinfo($file, PATHINFO_EXTENSION);
        if ($file === NULL || $fileType !== 'json') {
            $file = date('Y_m_d_H_i') . '.json';
        }
        $start      = '{';
        $dataString = '';
        $end        = '}';
        $jsonData   = $start . $dataString . $end;
        $i          = 0;
        if (!file_exists($file)) {#文件不存在
            file_put_contents($file, $jsonData);
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

        return $jsonData;
    }
}
