<?php
/**
 * @Desc
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * Created by PhpStorm
 * User: develop41
 * Date: 2019-01-02 15:30:41
 */

////////////////////////////////////////////////////////////////////
//                          _ooOoo_                               //
//                         o8888888o                              //
//                         88" . "88                              //
//                         (| ^_^ |)                              //
//                         O\  =  /O                              //
//                      ____/`---'\____                           //
//                    .'  \\|     |//  `.                         //
//                   /  \\|||  :  |||//  \                        //
//                  /  _||||| -:- |||||-  \                       //
//                  |   | \\\  -  /// |   |                       //
//                  | \_|  ''\---/''  |   |                       //
//                  \  .-\__  `-`  ___/-. /                       //
//                ___`. .'  /--.--\  `. . ___                     //
//            \  \ `-.   \_ __\ /__ _/   .-` /  /                 //
//      ========`-.____`-.___\_____/___.-`____.-'========         //
//                           `=---='                              //
//      ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^        //
//              佛祖保佑       永无BUG       永不修改               //
////////////////////////////////////////////////////////////////////

// QbtThink常量定义
const ONETHINK_VERSION    = '1.1.141101';
const ONETHINK_ADDON_PATH = './Addons/';

/*
    function AC('AddonName','KEY')
*/
if (!function_exists('AC')) {
    function AC($addonName = NULL, $key = NULL)
    {

        if ($addonName != NULL && $key != NULL) {

            $res  = M('addons')->field('config')->where("name = '" . $addonName . "'")->find();
            $res2 = json_decode($res['config'], TRUE);

            return $res2[$key];
        } else {
            return '';
        }
    }
}

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{

    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = NULL)
{

    $uid = is_null($uid) ? is_login() : $uid;

    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function str2arr($str, $glue = ',')
{

    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr2str($arr, $glue = ',')
{

    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str     需要转换的字符串
 * @param string $start   开始位置
 * @param string $length  截取长度
 * @param string $charset 编码格式
 * @param string $suffix  截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = TRUE)
{

    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    else if (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (FALSE === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }

    return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data   要加密的字符串
 * @param string $key    加密密钥
 * @param int    $expire 过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0)
{

    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }

    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function think_decrypt($data, $key = '')
{

    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(['-', '_'], ['+', '/'], $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }

    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{

    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名

    return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array  $list   查询结果
 * @param string $field  排序的字段名
 * @param array  $sortby 排序类型
 *                       asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{

    if (is_array($list)) {
        $refer = $resultSet = [];
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];

        return $resultSet;
    }

    return FALSE;
}

/**
 * 把返回的数据集转换成Tree
 * @param array  $list  要转换的数据集
 * @param string $pid   parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{

    // 创建Tree
    $tree = [];
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent           =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }

    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array  $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = [])
{

    if (is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }

    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{

    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;

    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 */
function set_redirect_url($url)
{

    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 */
function get_redirect_url()
{

    $url = cookie('redirect_url');

    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed  $params 传入参数
 * @return void
 */
function hook($hook, $params = [])
{

    \Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name)
{

    $class = "Addons\\{$name}\\{$name}Addon";

    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name)
{

    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();

        return $addon->getConfig();
    } else {
        return [];
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url   url
 * @param array  $param 参数
 */
function addons_url($url, $param = [])
{

    $url        = parse_url($url);
    $case       = C('URL_CASE_INSENSITIVE');
    $addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = [
        '_addons'     => $addons,
        '_controller' => $controller,
        '_action'     => $action,
    ];
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function time_format($time = NULL, $format = 'Y-m-d H:i')
{

    $time = $time === NULL ? NOW_TIME : intval($time);

    return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0)
{

    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
        if ($info && isset($info[1])) {
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }

    return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0)
{

    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if ($info !== FALSE && $info['nickname']) {
            $nickname = $info['nickname'];
            $name     = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }

    return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = NULL)
{

    static $list;

    /* 非法分类ID */
    if (empty($id) || !is_numeric($id)) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('sys_category_list');
    }

    /* 获取分类名称 */
    if (!isset($list[$id])) {
        $cate = M('Category')->find($id);
        if (!$cate || 1 != $cate['status']) { //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); //更新缓存
    }

    return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id)
{

    return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id)
{

    return get_category($id, 'title');
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id = NULL)
{

    $map = ['status' => 1, 'extend' => 0];
    if (!is_null($model_id)) {
        $map['id'] = ['neq', $model_id];
    }
    $model = M('Model')->where($map)->field(TRUE)->select();
    foreach ($model as $value) {
        $list[$value['id']] = $value;
    }

    return $list;
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = NULL, $field = NULL)
{

    static $list;

    /* 非法分类ID */
    if (!(is_numeric($id) || is_null($id))) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('DOCUMENT_MODEL_LIST');
    }

    /* 获取模型名称 */
    if (empty($list)) {
        $map   = ['status' => 1, 'extend' => 1];
        $model = M('Model')->where($map)->field(TRUE)->select();
        foreach ($model as $value) {
            $list[$value['id']] = $value;
        }
        S('DOCUMENT_MODEL_LIST', $list); //更新缓存
    }

    /* 根据条件返回数据 */
    if (is_null($id)) {
        return $list;
    } else if (is_null($field)) {
        return $list[$id];
    } else {
        return $list[$id][$field];
    }
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 */
function ubb($data)
{

    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action    行为标识
 * @param string $model     触发行为的模型名
 * @param int    $record_id 触发行为的记录id
 * @param int    $user_id   执行行为的用户id
 * @return boolean
 */
function action_log($action = NULL, $model = NULL, $record_id = NULL, $user_id = NULL)
{

    //参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id']   = $action_info['id'];
    $data['user_id']     = $user_id;
    $data['action_ip']   = ip2long(get_client_ip());
    $data['model']       = $model;
    $data['record_id']   = $record_id;
    $data['create_time'] = NOW_TIME;

    //解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user']   = $user_id;
            $log['record'] = $record_id;
            $log['model']  = $model;
            $log['time']   = NOW_TIME;
            $log['data']   = ['user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME];
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                } else {
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        //未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if (!empty($action_info['rule'])) {
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int    $self   替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 */
function parse_action($action = NULL, $self)
{

    if (empty($action)) {
        return FALSE;
    }

    //参数支持id或者name
    if (is_numeric($action)) {
        $map = ['id' => $action];
    } else {
        $map = ['name' => $action];
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();
    if (!$info || $info['status'] != 1) {
        return FALSE;
    }

    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules  = $info['rule'];
    $rules  = str_replace('{$self}', $self, $rules);
    $rules  = explode(';', $rules);
    $return = [];
    foreach ($rules as $key => &$rule) {
        $rule = explode('|', $rule);
        foreach ($rule as $k => $fields) {
            $field = empty($fields) ? [] : explode(':', $fields);
            if (!empty($field)) {
                $return[$key][$field[0]] = $field[1];
            }
        }
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
            unset($return[$key]['cycle'], $return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 * @param array $rules     解析后的规则数组
 * @param int   $action_id 行为id
 * @param array $user_id   执行的用户id
 * @return boolean false 失败 ， true 成功
 */
function execute_action($rules = FALSE, $action_id = NULL, $user_id = NULL)
{

    if (!$rules || empty($action_id) || empty($user_id)) {
        return FALSE;
    }

    $return = TRUE;
    foreach ($rules as $rule) {

        //检查执行周期
        $map                = ['action_id' => $action_id, 'user_id' => $user_id];
        $map['create_time'] = ['gt', NOW_TIME - intval($rule['cycle']) * 3600];
        $exec_count         = M('ActionLog')->where($map)->count();
        if ($exec_count > $rule['max']) {
            continue;
        }

        //执行数据库操作
        $Model = M(ucfirst($rule['table']));
        $field = $rule['field'];
        $res   = $Model->where($rule['condition'])->setField($field, ['exp', $rule['rule']]);

        if (!$res) {
            $return = FALSE;
        }
    }

    return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files)
{

    foreach ($files as $key => $value) {
        if (substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = NULL)
    {

        $result = [];
        if (NULL === $indexKey) {
            if (NULL === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (NULL === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }

        return $result;
    }
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 */
function get_table_name($model_id = NULL)
{

    if (empty($model_id)) {
        return FALSE;
    }
    $Model = M('Model');
    $name  = '';
    $info  = $Model->getById($model_id);
    if ($info['extend'] != 0) {
        $name = $Model->getFieldById($info['extend'], 'name') . '_';
    }
    $name .= $info['name'];

    return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = TRUE, $fields = TRUE)
{

    static $list;

    /* 非法ID */
    if (empty($model_id) || !is_numeric($model_id)) {
        return '';
    }

    /* 获取属性 */
    if (!isset($list[$model_id])) {
        $map    = ['model_id' => $model_id];
        $extend = M('Model')->getFieldById($model_id, 'extend');

        if ($extend) {
            $map = ['model_id' => ["in", [$model_id, $extend]]];
        }
        $info            = M('Attribute')->where($map)->field($fields)->select();
        $list[$model_id] = $info;
    }

    $attr = [];
    if ($group) {
        foreach ($list[$model_id] as $value) {
            $attr[$value['id']] = $value;
        }
        $model     = M("Model")->field("field_sort,attribute_list,attribute_alias")->find($model_id);
        $attribute = explode(",", $model['attribute_list']);
        if (empty($model['field_sort'])) { //未排序
            $group = [1 => array_merge($attr)];
        } else {
            $group = json_decode($model['field_sort'], TRUE);

            $keys = array_keys($group);
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    if (!in_array($val['id'], $attribute)) {
                        unset($attr[$key]);
                    }
                }
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }
        if (!empty($model['attribute_alias'])) {
            $alias  = preg_split('/[;\r\n]+/s', $model['attribute_alias']);
            $fields = [];
            foreach ($alias as &$value) {
                $val             = explode(':', $value);
                $fields[$val[0]] = $val[1];
            }
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    if (!empty($fields[$val['name']])) {
                        $value[$key]['title'] = $fields[$val['name']];
                    }
                }
            }
        }
        $attr = $group;
    } else {
        foreach ($list[$model_id] as $value) {
            $attr[$value['name']] = $value;
        }
    }

    return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string       $name 格式 [模块名]/接口名/方法名
 * @param  array|string $vars 参数
 */
function api($name, $vars = [])
{

    $array     = explode('/', $name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array ? array_pop($array) : 'Common';
    $callback  = $module . '\\Api\\' . $classname . 'Api::' . $method;
    if (is_string($vars)) {
        parse_str($vars, $vars);
    }

    return call_user_func_array($callback, $vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed  $value     条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field     需要返回的字段，不传则返回整个数据
 * @param string $table     需要查询的表
 */
function get_table_field($value = NULL, $condition = 'id', $field = NULL, $table = NULL)
{

    if (empty($value) || empty($table)) {
        return FALSE;
    }

    //拼接参数
    $map[$condition] = $value;
    $info            = M(ucfirst($table))->where($map);
    if (empty($field)) {
        $info = $info->field(TRUE)->find();
    } else {
        $info = $info->getField($field);
    }

    return $info;
}

/**
 * 获取链接信息
 * @param int    $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 */
function get_link($link_id = NULL, $field = 'url')
{

    $link = '';
    if (empty($link_id)) {
        return $link;
    }
    $link = M('Url')->getById($link_id);
    if (empty($field)) {
        return $link;
    } else {
        return $link[$field];
    }
}

/**
 * 获取文档封面图片
 * @param int    $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 */
function get_cover($cover_id, $field = NULL)
{

    if (empty($cover_id)) {
        return FALSE;
    }
    $picture = M('Picture')->where(['status' => 1])->getById($cover_id);
    if ($field == 'path') {
        if (!empty($picture['url'])) {
            $picture['path'] = $picture['url'];
        } else {
            $picture['path'] = __ROOT__ . $picture['path'];
        }
    }

    return empty($field) ? $picture : $picture[$field];
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos     推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 */
function check_document_position($pos = 0, $contain = 0)
{

    if (empty($pos) || empty($contain)) {
        return FALSE;
    }

    //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if ($res !== 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * 获取数据的所有子孙数据的id值
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */

function get_stemma($pids, Model &$model, $field = 'id')
{

    $collection = [];

    //非空判断
    if (empty($pids)) {
        return $collection;
    }

    if (is_array($pids)) {
        $pids = trim(implode(',', $pids), ',');
    }
    $result    = $model->field($field)->where(['pid' => ['IN', (string)$pids]])->select();
    $child_ids = array_column((array)$result, 'id');

    while (!empty($child_ids)) {
        $collection = array_merge($collection, $result);
        $result     = $model->field($field)->where(['pid' => ['IN', $child_ids]])->select();
        $child_ids  = array_column((array)$result, 'id');
    }

    return $collection;
}

/**
 * 验证分类是否允许发布内容
 * @param  integer $id 分类ID
 * @return boolean     true-允许发布内容，false-不允许发布内容
 */
function check_category($id)
{

    if (is_array($id)) {
        $id['type'] = !empty($id['type']) ? $id['type'] : 2;
        $type       = get_category($id['category_id'], 'type');
        $type       = explode(",", $type);

        return in_array($id['type'], $type);
    } else {
        $publish = get_category($id, 'allow_publish');

        return $publish ? TRUE : FALSE;
    }
}

/**
 * 检测分类是否绑定了指定模型
 * @param  array $info 模型ID和分类ID数组
 * @return boolean     true-绑定了模型，false-未绑定模型
 */
function check_category_model($info)
{

    $cate  = get_category($info['category_id']);
    $array = explode(',', $info['pid'] ? $cate['model_sub'] : $cate['model']);

    return in_array($info['model_id'], $array);
}

/**
 * @ideas json格式返回
 * @param array  $data 数据
 * @param int    $code 状态码
 * @param string $msg  提示信息
 */
function jsonReturn(array $data = NULL, $code = 200, $msg = '操作成功')
{

    header('Content-type: application/json'); //设置json解析
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
 * @desc对数据进行编码转换
 * @param array/string $data       数组
 * @param string $output 转换后的编码
 * @return array|null|string|string[]
 */
function array_iconv(&$data, $output = 'utf-8')
{

    $encode_arr = ['UTF-8', 'ASCII', 'GBK', 'GB2312', 'BIG5', 'JIS', 'eucjp-win', 'sjis-win', 'EUC-JP'];
    $encoded    = mb_detect_encoding($data, $encode_arr);//自动判断编码

    if (!is_array($data)) {
        return mb_convert_encoding($data, $output, $encoded);
    } else {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $data[$key] = array_iconv($val, $output);
            } else {
                $data[$key] = mb_convert_encoding($data, $output, $encoded);
            }
        }

        return $data;
    }
}

/**获取目录下所有文件信息
 * @param string $filedir 目录名
 * @return array   返回该目录下所有文件的信息，普通文件返回文件名(全名),安卓安装包则包含包名，版本号等信息
 */
function showDir($filedir)
{

    //打开目录
    $dir    = @ dir($filedir);
    $info   = [];
    $appObj = new Admin\Controller\ApkParser();
    //列出目录中的文件
    while (($file = $dir->read()) !== FALSE) {
        if (is_file($filedir . $file)) {
            $sha                 = sha1($file);
            $info[$sha]['name']  = $file;
            $info[$sha]['sha']   = $sha;
            $info[$sha]['ctime'] = date('Y-m-d H:i:s', filemtime($filedir . $file));
            //解析后缀
            if (strtolower(end(explode('.', $file))) === 'apk') {
                if (!$appObj->open($filedir . $file)) continue; //解析安装包
                $info[$sha]['packge']['app_name']     = $appObj->getAppName();     // 应用名称
                $info[$sha]['packge']['packge_name']  = $appObj->getPackage();    // 应用包名
                $info[$sha]['packge']['version_name'] = $appObj->getVersionName();  // 版本名称
                $info[$sha]['packge']['version_code'] = $appObj->getVersionCode();  // 版本代码
            }
        }
    }
    $dir->close();

    return $info;
}

/**
 * @desc签名参数
 * @param        $params
 * @param string $sign
 * @return mixed
 */
function sign($params, $sign = 'sign')
{

    $params['key'] = 'e10adc3949ba59abbe56e057f20f883e';
    ksort($params);
    $str = '';
    foreach ($params as $key => $val) {
        $str .= $key . "={$val}&";
    }
    $str           = trim($str, '&');
    $params[$sign] = md5($str);
    unset($params['key']);

    return $params;
}

/**
 * @ideas  格式化时间 默认 2018-05-20 13:14:00 格式
 * @param  int|string $type 时间类型
 * @param  int        $time 时间戳
 * @return false|string 时间
 */
function format_time($time = NULL, $type = 0)
{

    if ($time === NULL) {
        $time = time();
    } else if (strlen($time) < 2) {
        $type = $time;
        $time = time();

    }
    $farmat      = NULL;
    $farmat_list = [
        'Y-m-d',
        'Y-m-d H',
        'Y-m-d H:i',
        'Y-m-d H:i:s',
    ];
    if (is_numeric($type)) {
        switch ($type) {
            case '1':
                $farmat = $farmat_list[0];
                break;
            case '2':
                $farmat = $farmat_list[1];
                break;
            case '3':
                $farmat = $farmat_list[2];
                break;
            default:
                $farmat = $farmat_list[3];
                break;
        }

        return date($farmat, $time);
    }

    return date($type, $time);

}

/**
 * @desc计算时间差:默认返回类型为：分钟
 * @param   string $old_time    只能是时间戳 ,
 * @param   string $return_type 为 h 是小时,为 s 是秒
 * @return  float
 */
function timecount($old_time, $return_type = 'm')
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
 * @desc多维变一维
 * @param array $arr 多维数组
 * @return array|bool
 */
function arrToOne(array $arr)
{

    static $tmp = [];
    if (!is_array($arr)) {
        return FALSE;
    }
    foreach ($arr as $val) {
        if (is_array($val)) {
            \arrToOne($val);
        } else {
            $tmp[] = $val;
        }
    }

    return $tmp;
}

/**
 * @Desc   多维变一维
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param array $arr       多维数组
 * @param bool  $recursive 是否递归
 * @return array
 */
function arrMoreToOne(array &$arr, $recursive = NULL)
{

    $result = [];
    switch ($recursive) {
        case FALSE:
            array_walk($arr, function ($value) use (&$result) {

                $result[] = $value;
            });
            break;
        case TRUE:
            array_walk_recursive($arr, function ($value) use (&$result) {

                $result[] = $value;
            });
            break;
        default:
            array_walk_recursive($arr, function ($value) use (&$result) {

                $result[] = $value;
            });
            break;
    }

    /*if ($recursive === FALSE) {
        array_walk($arr, function ($value) use (&$result) {

            $result[] = $value;
        });
    } else {
        array_walk_recursive($arr, function ($value) use (&$result) {

            $result[] = $value;
        });
    }*/

    return $arr = $result;
}

/**
 * @desc把json字符串转数组
 * @param $p
 * @return mixed
 */
function json_to_array($p)
{

    if (mb_detect_encoding($p, ['ASCII', 'UTF-8', 'GB2312', 'GBK']) !== 'UTF-8') {
        $p = iconv('GBK', 'UTF-8', $p);
    }

    return json_decode($p, TRUE);
}

/**
 * @desc   获取当前用户角色ID
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param  null $uid
 * @return mixed
 */
function get_group_id($uid = NULL)
{

    $uid = empty($uid) ? is_login() : $uid;

    $group_id = M('auth_group_access')->where(['uid' => $uid])->getField('group_id');

    return empty($group_id) ? 0 : $group_id;
}

/**
 * @ideas 删除数组空值
 * @param array $arr 需去空数组
 * @return mixed 返回原数组
 */
function unsetEmptyArray(array &$arr)
{

    if (is_array($arr)) {
        foreach ($arr as $item => $value) {
            if (empty($value)) {
                unset($arr[$item]);
            }
        }
    }

    return $arr;
}

/**
 * @desc去掉数组空格
 * @param $arr
 * @return string
 */
function arrayTrim(&$arr)
{

    foreach ($arr as $i => $item) {
        $arr[$i] = trim($item, ' ');
    }

    return $arr;
}

/**
 * 对象 转 数组
 *
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj)
{

    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}

/**
 * @desc连接redis
 * @return \Redis
 */
function redis()
{

    static $redis = NULL;
    if ($redis === NULL) {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        //$redis->auth(15679288058);
    }

    return $redis;
}

/**
 * @Desc   redis排队
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param  mixed  $params
 * @param  string $key
 * @param  int    $expire 过期时间
 * @return bool
 */
function redisQueue($params, $key = 'key', $expire = 300)
{

    try {
        $redis = redis();//连接redis
        $redis->lPush($key, json_encode($params));// 序列化加入队列
        if ($expire > 0) {#设置过期时间
            $redis->expire($key, $expire);
        }

        return TRUE;
    } catch (\Exception $e) {
        printf($e->getMessage());
    }

    return FALSE;
}

/**
 * @Desc   redis出列
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param string $key
 * @return array
 */
function redisDequeue($key = 'key')
{

    $http_code        = [];
    $command          = getCommand();
    $content          = NULL;
    $msg_content      = NULL;
    $registration_ids = NULL;
    $type             = 'android';
    $client           = pushInit();
    $redis            = redis();//连接redis
    $lLen             = $redis->lLen($key);//获取该队列长度
    if ($lLen > 0) {
        for ($i = 0; $i < $lLen; $i++) {
            //用于移除并返回列表的最后一个元素。
            $list             = $redis->rPop($key);
            $param            = json_decode($list);//反序列化
            $content          = $param->data;
            $msg_content      = $param->command;
            $registration_ids = $param->registration_ids;
            try {
                $data = [
                    'title'        => $command[$msg_content],//消息标题
                    'content_type' => 'text',//消息内容类型
                    'extras'       => object_to_array($content),//表示扩展字段，接受一个数组，自定义 Key/value 信息以供业务使用
                ];
                if (empty($registration_ids)) {//推送所有的设备
                    $response = $client->push()->setPlatform($type)->addAllAudience()->message($msg_content, $data)->send();
                } else {
                    $response = $client->push()->setPlatform($type)->addAlias($registration_ids)->message($msg_content, $data)->send();
                }
                $http_code[] = $response['http_code'];
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                return $e->getCode();
            } catch (\JPush\Exceptions\APIRequestException $e) {
                return $e->getHttpCode();
            } catch (\Exception $e) {
                return $e->getCode();
            }
        }
    }

    return $http_code;
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
function interfaceCurrentLimiting($usb, $count = 10, $timeed = 60, $type = 's', $temps = 60)
{

    $ip    = get_client_ip();//获取ip地址
    $redis = redis();//连接redis
    $len   = $redis->lLen($ip . $usb);//队列长度
    if ($len < $count) {//调用小于10次
        $redis->lPush($ip . $usb, time());//入队
        $redis->expire($ip . $usb, $timeed);//用于设置 key 的过期时间。key 过期后将不再可用。
    } else {//调用大于10次
        $last_time = $redis->lIndex($ip . $usb, 0);//最后一次调用时间
        $time      = timecount($last_time, $type);//返回最后一次调用时间离现在多少s
        if ($time < $temps) {//小于60s
            \jsonReturn(403, '请求频繁，请稍候再试');
        }
    }

    return $len;
}

/**
 * @param   string $url            网址
 * @param   bool   $ssl            是否为https
 * @param   int    $contentTimeOut 连接超时时间
 * @return  bool|mixed  返回数据
 */
function curlGet($url, $ssl = TRUE, $contentTimeOut = 30)
{

    // curl完成
    $curl = curl_init();
    //设置curl选项
    curl_setopt($curl, CURLOPT_URL, $url);//URL
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
    curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
    curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);//referer头，请求来源
    curl_setopt($curl, CURLOPT_TIMEOUT, $contentTimeOut);//设置超时时间
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
 * @param   string $url            网址
 * @param   array  $data           数据
 * @param   bool   $ssl            是否为https
 * @param   int    $contentTimeOut 连接超时时间
 * @return  bool|mixed 返回数据
 */
function curlPost($url, $data, $ssl = TRUE, $contentTimeOut = 30)
{

    // curl完成
    $curl = curl_init();
    //设置curl选项
    curl_setopt($curl, CURLOPT_URL, $url);//URL
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
    curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
    curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);//referer头，请求来源
    curl_setopt($curl, CURLOPT_TIMEOUT, $contentTimeOut);//设置超时时间
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

/**
 * @desc 初始化极光推送
 * @return \JPush\Client
 */
function pushInit()
{

    $app_key       = '9cff4ed396e859554875589f';
    $master_secret = '84fb1cbff0bec1f149e1f7fa';
    vendor('jpush.autoload');

    return new \JPush\Client($app_key, $master_secret, '', '', 'BJ');

}

/**
 * @Desc   查询指定设备的别名
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param $client
 * @param $registration_id
 * @param $type
 * @return bool
 */
function checkRegistration_id($client, $registration_id, $type = 'android')
{

    $device = $client->device();
    $res    = $device->getAliasDevices($registration_id, $type);
    if (!empty($res['body']['registration_ids']) && $res['http_code'] === 200) {
        return $registration_id;
    }

    return FALSE;
}

/**
 * @Desc   推送命令
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @return array
 */
function getCommand()
{

    return [
        'SEND_IMG'                => '朋友圈图文发布',
        'SHARELINK'               => '朋友圈分享链接',
        'ADD_FRIENDS'             => '通讯录加好友',
        'ZOMBIE_POWDER'           => '删除僵尸粉',
        'ONLINE_CHECK'            => '离线检测',
        'TASK_CHECK'              => '任务繁忙检测',
        'AUTO_ADD_FRIENDS'        => '自动通过好友开启',
        'AUTO_ADD_FRIENDS_CLOSE'  => '自动通过好友关闭',
        'AUTO_RECEIVE'            => '设备智能回复开启',
        'AUTO_RECEIVE_CLOSE'      => '设备智能回复关闭',
        'CUSTOMIZE_RECEIVE'       => '设备自定义回复开启',
        'CUSTOMIZE_RECEIVE_CLOSE' => '设备自定义回复关闭',
        'PUSH_PACKAGE'            => '安卓包推送',
        'SHUTDOWN'                => '一键关机',
        'RESTART'                 => '一键重启',
        'SEND_LABLE'              => '标签检测',
        'RESET'                   => '一键重置',
        'CLEAR_ALL_LINK'          => '清空所有链接',
        'test'                    => '测试专用',
    ];
}

/**
 * @Desc   极光推送
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param  string $msg_content      消息内容本身
 * @param  array  $content          需要处理的数据
 * @param  array  $registration_ids 极光推送的设备唯一性标识或者别名 RegistrationID
 * @return int|mixed http_code 返回的数据格式
 */
function pushMsg($msg_content, $content, $registration_ids = [])
{

    try {
        $command = getCommand();
        $client  = pushInit();
        $data    = [
            'title'        => $command[$msg_content],//消息标题
            'content_type' => 'text',//消息内容类型
            //表示扩展字段，接受一个数组，自定义 Key/value 信息以供业务使用
            'extras'       => $content,
        ];
        if (empty($registration_ids)) {//推送所有的设备
            $response = $client->push()->setPlatform('android')->addAllAudience()->message($msg_content, $data)->send();
        } else {
            $response = $client->push()->setPlatform('android')->addAlias($registration_ids)->message($msg_content, $data)->send();
        }

        return $response['http_code'];
    } catch (\JPush\Exceptions\APIConnectionException $e) {
        return $e->getCode();
    } catch (\JPush\Exceptions\APIRequestException $e) {
        return $e->getHttpCode();
    } catch (\Exception $e) {
        return $e->getCode();
    }
}

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
    $url_arr         = parse_url($url);
    $errno           = NULL;
    $errstr          = NULL;
    $url_arr['port'] = -1;
    $port            = isset($url_arr['port']) ? $url_arr['port'] : 80;
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
    $header = "$method  $getPath  HTTP/1.1\r\n";
    $header .= "Host: " . $url_arr['host'] . "\r\n";
    if (!empty($post_data)) {  //传递post数据
        /*$_post = [];
        foreach ($post_data as $_k => $_v) {
            $_post[] = $_k . "=" . urlencode($_v);
        }
        $_post    = implode('&', $_post);*/
        $_post    = http_build_query($post_data);
        $post_str = "Content-Type:application/x-www-form-urlencoded; charset=UTF-8\r\n";
        $post_str .= "Content-Length: " . strlen($_post) . "\r\n";  //数据长度
        $post_str .= "Connection:Close\r\n\r\n";
        $post_str .= $_post;  //传递post数据
        $header   .= $post_str;
    } else {
        $header .= "Connection:Close\r\n\r\n";
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
            //        print "一个请求下载完成!\n";
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
        echo "* Process {$pid} was created, and Executed:\n\n";
        eval($input); //解析命令
        exit;
    } else {//主进程
        $pid = pcntl_wait($status, WUNTRACED); //取得子进程结束状态
        if (pcntl_wifexited($status)) {
            echo "\n\n* Sub process: {$pid} exited with {$status}";
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
            echo "create process error！\n";
            exit(1);
        } //主进程，获得子进程pid
        else if ($processid) {
            $task++; //下一个任务
            $currentProcessid = posix_getpid(); //当前进程的Id
            $parentProcessid  = posix_getppid(); // 父级进程的ID
            $phpProcessid     = getmypid(); //当前php进程的id
            echo "task:", $task, "\tprocessid:", $processid, "\tcurrentProcessid:", $currentProcessid, "\tparentProcessid:", $parentProcessid, "\tphpProcessid:", $phpProcessid, "\n";
            //控制进程数
            if ($task >= $processNumLimit) {
                echo "wait chl start！\n";
                $exitid = pcntl_wait($status); //等待退出
                echo "wait chl end！extid:", $exitid, "\tstatus:", $status, "\n";
            }

            //任务总量控制
            if ($task >= $taskNum) {
                echo "taskNum enough！\n";
                break;
            }
        } //processid=0为新创建的进程
        else {
            $currentProcessid = posix_getpid(); //当前进程的Id
            $parentProcessid  = posix_getppid(); // 父级进程的ID
            $phpProcessid     = getmypid(); //当前php进程的id
            echo "task:", $task, "\tprocessid:", $processid, "\tcurrentProcessid:", $currentProcessid, "\tparentProcessid:", $parentProcessid, "\tphpProcessid:", $phpProcessid, "\tbegin!\n";

            echo "task:", $task, "\tprocessid:", $processid, "\tcurrentProcessid:", $currentProcessid, "\tparentProcessid:", $parentProcessid, "\tphpProcessid:", $phpProcessid, "\tend!\n";

            exit(0); //子进程执行完后退出，防止进入循环创建子进程
        }
    }
}

/**
 * @desc校验URL地址
 * @param $url
 * @return int
 */
function checkUrl($url)
{

    return ereg("^(http)s? ://(www\.)?.+(com|net|org)$", $url);
}

/**
 * @param   string $table 表名
 * @param   array  $where 查询条件
 * @param   string $field 查询字段
 * @param   string $order 字段排序规则
 * @param   int    $num   分页显示数目
 * @return  array        返回分页和查询数据
 */
function page($table = NULL, $where = [], $field = '', $order = 'id desc', $num = 10, $rollPage = 10)
{

    try {
        $count = M($table)->where($where)->count();// 查询满足要求的总记录数
        $Page  = new \Think\Page($count, $num, [], $rollPage);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $Page->setConfig('next', '下一页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('header', '<div class="page_s1"><span class="right1">共<b>%TOTAL_ROW%</b>条记录</span> <span class="left1">第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</span></div>');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = FALSE;//最后一页不显示为 总页数
        $show             = $Page->show();// 分页显示输出
        $list             = M($table)->order($order)->where($where)->field($field)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        if ($count < $num) {
            unset($show);
        }

        return [
            'page'  => $show,
            'list'  => $list,
            'count' => $count,
        ];
    } catch (\Exception $e) {
        printf($e->getMessage());
    }

}

/**
 * @desc
 * @param $arr
 * @param $num
 * @return array|bool
 */
function array_chunks($arr, $num)
{

    $result     = [];
    $arr_num    = count($arr);
    $num        = (int)$num > 0 ? $num : 1;#分组个数
    $group_nums = (int)($arr_num / $num); #每组个数
    if (is_array($arr) && !empty($arr)) {
        if ($arr_num < $num) {
            #数组个数不足分组个数
            return $result[] = array_chunk($arr, 1);
        }
        $res = array_chunk($arr, $group_nums);
        foreach ($res as $k => $v) {
            if ($k < $num - 1) {
                $result[] = $v;
            } else {
                $a[] = $v;
            }
        }
        $result[] = arrToOne($a);

        return $result;
    }

    return FALSE;
}

/**
 * @desc 数组拆分
 * @param array $arr 拆分数组
 * @param int   $num 拆分个数
 * @return array
 */
function arrayChunk(&$arr, $num)
{

    $arrCopy = $arr;
    $arr     = [];
    array_walk($arrCopy, function (&$value, $key, $num) use (&$arr) {

        $index = $key / $num;
        if ($key < $num) {
            $arr[$key][] = $value;
        }
        if ($index >= 1) {
            $keys = explode('.', $index);
            $i    = $key - $keys[0] * $num;
            \array_push($arr[$i], $value);
            ++$i;
        }
    }, $num);

    return $arr;
}

/**
 * @Desc   权限检测
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param null $uid
 */
function checkPermission($uid = NULL)
{

    $rule = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME);
    #所属角色组
    $uid = empty($uid) ? is_login() : $uid;
    if ($uid === '1') {#超管权限不限
        die(json_encode(['info' => '已授权访问', 'status' => 1, 'url' => '',]));
    } else {
        $group_id   = M('auth_group_access')->where(['uid' => $uid])->getField('group_id');
        $auth_group = M('AuthGroup')->where(['id' => $group_id, 'status' => ['egt', '0'], 'module' => 'admin', 'type' => 1])->getfield('id,title,rules');
        $rules      = \explode(',', $auth_group[$group_id]['rules']);#角色规则
        $url_id     = M('AuthRule')->where(['name' => $rule])->getField('id');#获取规则地址ID
        if (!in_array($url_id, $rules, TRUE)) {
            die(json_encode(['info' => '未授权访问!', 'status' => 0, 'url' => '',]));
        }
        die(json_encode(['info' => '已授权访问', 'status' => 1, 'url' => '',]));
    }
}

/**
 * @ideas 验证手机号
 * @param $phone
 * @return bool|false|int
 */
function checkPhone($phone)
{

    $checkPhone = preg_match('/^1[3-9]\d{9}$/', $phone);
    if ($checkPhone === 1) {
        return $checkPhone;
    }

    return FALSE;

}

/**
 * @desc   Excel导出
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param  array  $data
 * @param  string $fileName
 * @param  array  $header
 * @param  array  $index
 */
function dumpExcel(array $data, $fileName = NULL, $header = [], $index = [])
{

    $fileNames = $fileName;
    header('Content-type:application/vnd.ms-excel');
    header('Content-Disposition:filename=' . $fileNames . '.xls');
    $table_header = implode("\t", $header);
    $strExport    = $table_header . "\r";
    foreach ($data as $row) {
        foreach ($index as $val) {
            $strExport .= $row[$val] . "\t";
        }
        $strExport = rtrim($strExport) . "\r";
    }
    $strExport = iconv('UTF-8', "GB2312//IGNORE", $strExport);
    exit($strExport);
}

/**
 * @Desc   执行swoole tcp 客户端发送数据
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param null $path
 * @return bool
 */
function execSwoole($path = NULL)
{

    if ($path === NULL) {
        $path = 'Application/Admin/Controller/SwooleClientController.class.php';
    }
    exec($path);

    return TRUE;
}

/**
 * @Desc   多维数组排序
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param  array  $data             排序数组
 * @param  string $sort_order_field 排序字段
 * @param  int    $sort_order
 * @param  int    $sort_type
 * @return mixed
 */
function array_order($data, $sort_order_field, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
{

    foreach ($data as $val) {
        $key_arrays[] = $val[$sort_order_field];
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $data);

    return $data;
}
