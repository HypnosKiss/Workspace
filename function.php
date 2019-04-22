<?php
// +----------------------------------------------------------------------
// | QbtThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qbt8.com All rights reserved.
// +----------------------------------------------------------------------

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
 * Bill账户明细类型
 * RECHARGE为充值，CASH为提现，BUY为下单消费，INVITE为邀请奖励，SEND为配送所得
 */
function account_type($type)
{

    $actype = [
        'REG_GOLD'    => ['注册奖励', '+'],
        'RECHARGE'    => ['充值', '+'],
        'CASH'        => ['提现', '-'],
        'BUY'         => ['下单消费', '-'],
        'INVITE'      => ['邀请奖励', '+'],
        'SEND'        => ['配送所得', '+'],
        'REFUND'      => ['取消订单退款', '+'],
        'CUT_PAYMENT' => ['取消订单扣款', '-'],
    ];

    return $actype[$type];
}

/**
 * 身份信息上传多张/单张图片
 * */
function Upload_imgs($root_path, $filedir)
{

    if ($_FILES) {
        if (!is_dir($root_path)) {
            mkdir($root_path, 0777, TRUE);
            //mkdir($root_path);
        }
        //上传图片
        $upload           = new \Think\Upload();// 实例化上传类
        $upload->maxSize  = 100048000;// 设置附件上传大小 100M
        $upload->exts     = ['jpg', 'gif', 'png', 'jpeg', "mp4", "wmv"];// 设置附件上传类型
        $upload->rootPath = $root_path . '/'; // 设置附件上传根目录
        $info             = $upload->upload($_FILES);
        if ($info) {
            $json = [];
            foreach ($info as $key => $val) {
                $json[$key] = $filedir . $val['savepath'] . $val['savename'];
            }

            return json_encode($json);
        } else {
            return FALSE;
        }
    }

}

/**
 * 银行卡号识别银行名称
 * */
//银行卡号验证
FUNCTION CHECKBANKINFO($CARD)
{

    HEADER('CONTENT-TYPE:TEXT/HTML;CHARSET=UTF-8');
    REQUIRE_ONCE('bankList.PHP');
    $CARD_8 = SUBSTR($CARD, 0, 8);
    IF (ISSET($BANKLIST[$CARD_8])) {
        RETURN $BANKLIST[$CARD_8];
    }
    $CARD_6 = SUBSTR($CARD, 0, 6);
    IF (ISSET($BANKLIST[$CARD_6])) {
        RETURN $BANKLIST[$CARD_6];
    }
    $CARD_5 = SUBSTR($CARD, 0, 5);
    IF (ISSET($BANKLIST[$CARD_5])) {
        RETURN $BANKLIST[$CARD_5];
    }
    $CARD_4 = SUBSTR($CARD, 0, 4);
    IF (ISSET($BANKLIST[$CARD_4])) {
        RETURN $BANKLIST[$CARD_4];
    }

    RETURN FALSE;//ECHO '该卡号信息不存在';
}

/**
 *msg
 *消息发送
 */
function send_msg_content($uid, $type, $content)
{

    $data = [
        'uid'         => $uid,
        'type'        => $type,
        'content'     => $content,
        'create_time' => time(),
    ];

    return M('Msg')->add($data);
}

/**
 * 身份证信息验证
 **/
/*严格验证身份证号码*/

function checkIdCard($id_card)
{

    if (strlen($id_card) == 18) {
        return idcard_checksum18($id_card);
    } else if ((strlen($id_card) == 15)) {
        $id_card = idcard_15to18($id_card);

        return idcard_checksum18($id_card);
    } else {
        return FALSE;
    }
}

// 计算身份证校验码，根据国家标准GB 11643-1999
function idcard_verify_number($idcard_base)
{

    if (strlen($idcard_base) != 17) {
        return FALSE;
    }
    //加权因子
    $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    //校验码对应值
    $verify_number_list = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
    $checksum           = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod           = $checksum % 11;
    $verify_number = $verify_number_list[$mod];

    return $verify_number;
}

// 将15位身份证升级到18位
function idcard_15to18($idcard)
{

    if (strlen($idcard) != 15) {
        return FALSE;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), ['996', '997', '998', '999']) !== FALSE) {
            $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
        } else {
            $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . idcard_verify_number($idcard);

    return $idcard;
}

// 18位身份证校验码有效性检查
function idcard_checksum18($idcard)
{

    if (strlen($idcard) != 18) {
        return FALSE;
    }
    $idcard_base = substr($idcard, 0, 17);
    if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
        return FALSE;
    } else {
        return TRUE;
    }
}

//根据身份证号计算年龄
function getAgeByID($id)
{

    if (!checkIdCard($id)) return '';
    //过了这年的生日才算多了1周岁
    if (empty($id)) return '';
    $date = strtotime(substr($id, 6, 8));
    //获得出生年月日的时间戳
    $today = strtotime('today');
    //获得今日的时间戳
    $diff = floor(($today - $date) / 86400 / 365);
    //得到两个日期相差的大体年数
    //strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
    $age = strtotime(substr($id, 6, 8) . ' +' . $diff . 'years') > $today ? ($diff + 1) : $diff;

    return $age;
}

//根据身份证号区分性别
function get_xingbie($cid)
{

    //根据身份证号，自动返回性别
    if (!checkIdCard($cid)) return '';
    if (strlen($cid) == 18) {
        $sexint = (int)substr($cid, 16, 1);
    } else if ((strlen($cid) == 15)) {
        $sexint = (int)substr($cid, 14, 1);
    }

    return $sexint % 2 === 0 ? '女' : '男';
}

/**
 * 生成优惠券号码
 */
function make_coupon_card()
{

    mt_srand((double)microtime() * 10000);
    $charid = strtoupper(md5(uniqid(rand(), TRUE)));
    $hyphen = chr(45);// "-"
    $id     = //chr(123)// "{"
        substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12);

    //.chr(125);// "}"
    return $id;
}

/**
 * 产生优惠券
 */
function create_coupon($data, $ns)
{

    if ($ns > 1) {
        for ($i = 0; $i < $ns; $i++) {
            $coupon_id         = '';
            $coupon_id         = make_coupon_card();
            $data['coupon_id'] = $coupon_id;
            $falg              = M('Coupon')->add($data);
        }

        return TRUE;
    } else {
        $coupon_id         = make_coupon_card();
        $data['coupon_id'] = $coupon_id;
        $falg              = M('Coupon')->add($data);

        return TRUE;
    }

    return FALSE;
}

/*获取二维码*/
function make_ewmimg($url, $path, $flas = FALSE)
{

    vendor("phpqrcode.phpqrcode");
    $data = $url;
    // 纠错级别：L、M、Q、H
    $level = 'L';
    // 点的大小：1到10,用于手机端4就可以了
    $size = 7;
    // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
    //$path = $path;
    // 生成的文件名
    $fileName = dirname(dirname(dirname(__DIR__))) . $path;
    if ($flas) {
        \QRcode::png($data, $fileName, $level, $size);
    } else {
        if (!is_file($filename)) {
            \QRcode::png($data, $fileName, $level, $size);
        }
    }

    return $path;
}

/*极光推送*/
function qmks_jpush($order)
{

    vendor("jgpush.autoload");
    $config        = api('Config/lists');
    $app_key       = '1fa91ac07b5520a48f2a249b';//'31c51b24cbd43e6bf88e6971';//
    $master_secret = '6854c8e07d43bcbf2eafbbb9';//'179ac3ac9090b4ab806d2eba';//
    $shipper       = M('Shipper')->where(['id' => $order['shipper_id']])->find();
    $add_money     = M('payment')->where(['order_id' => $order['order_id'], 'state' => 'pay'])->sum('money');
    //备注：音频或文字消息
    if ($order['remark_type'] == 'voice') {
        $msg = $config['APP_URL'] . $order['remark'];
    } else {
        $msg = $order['remark'];
    }
    $tools   = M('Tools')->where(['id' => $order['tools_id']])->getField('name');
    $goods   = M('Tools')->where(['id' => $order['goods_id']])->getField('name');
    $special = $order['special_id'] > 0 ? '保温箱配送' : '';
    $client  = new \JPush\Client($app_key, $master_secret);
    $result  = $client->push()->setPlatform('all')->addAllAudience()->message('新订单ID:' . $order['order_id'],
                                                                              [
                                                                                  'title'        => '支付成功的新订单',
                                                                                  'content_type' => 'text',
                                                                                  'extras'       => [
                                                                                      'order_id'    => $order['order_id'],
                                                                                      'type'        => $order['type'] == 'today' ? '今日达' : '加急件',//订单类型type
                                                                                      'addr'        => [
                                                                                          'city' => $shipper['city'],
                                                                                          'area' => $shipper['name'],
                                                                                          'addr' => $shipper['address'],
                                                                                          'more' => $shipper['desc'],
                                                                                      ],//发货地址(city城市,area区,addr地址,more详细)
                                                                                      'take_grid'   => $shipper['take_grid'],//取货地点经纬度
                                                                                      'raddr'       => [
                                                                                          'city' => $order['recity'],
                                                                                          'area' => $order['rename'],
                                                                                          'addr' => $order['readdress'],
                                                                                          'more' => $order['redesc'],
                                                                                      ],//收货地址(city城市,area区,addr地址,more详细)
                                                                                      'has_grid'    => $order['has_grid'],//收货(送货)地点经纬度
                                                                                      'pickup_time' => $order['pickup_time'] == '立即取货' ? '立即取货' : getpickup_time($order['pickup_time']),//取货时间
                                                                                      'amount'      => $order['realmoney'],//round((double)$order['totmoney']-(double)$order['coupon'],2),//加价前订单金额
                                                                                      'add_money'   => $add_money,//加价金额
                                                                                      'remark'      => [
                                                                                          'type' => $order['remark_type'],
                                                                                          'msg'  => $msg,
                                                                                      ],//备注 type类型(remark-默认文字备注，voice-语音备注)msg消息内容或音频url
                                                                                      'tools'       => $tools,//运输工具
                                                                                      'goods'       => $goods,//物品类型
                                                                                      'special'     => $special,//特殊要求
                                                                                      'totkg'       => $order['totkg'],//物品重量
                                                                                      'price'       => $order['price'],//物品价格
                                                                                      'totkm'       => $order['totkm'],//运送总路程
                                                                                      'notice_type' => '1',
                                                                                  ],
                                                                              ])->send();

    return TRUE;
}

//收货时间转换'2017-07-17 20:00'
function getpickup_time($time)
{

    $tmp = strtotime(substr($time, 0, 10)) - time(date('Ymd'));
    if ($tmp >= 3600 * 48) {
        return '后天' . substr($time, 11);
    } else if ($tmp >= 3600 * 24) {
        return '明天' . substr($time, 11);
    } else {
        return '今天' . substr($time, 11);
    }
}

/**
 * @ideas 转换时间为 今天、明天、后天
 * @param $time
 * @return string
 */
function getDates($time)
{

    $tmp                  = \strtotime($time);
    $today                = \strtotime('today');//今天0点
    $tomorrow             = \strtotime('+1day');//明天0点
    $after_tomorrow       = \strtotime('+2day');//后天0点
    $three_after_tomorrow = \strtotime('+3day');//大后天0点
    if ($today <= $tmp && $tmp < $tomorrow) {
        $day = '今天';
    } else if ($tomorrow <= $tmp && $tmp < $after_tomorrow) {
        $day = '明天';
    } else if ($after_tomorrow <= $tmp && $tmp < $three_after_tomorrow) {
        $day = '后天';
    } else {
        $day = '大后天';
    }

    return $day . \date('H:i:s', $tmp);

}

function getDate($time)
{

    $nowDay = date('d');
    $day    = date('d', $time);
    $dayArr = ['今天', '明天', '后天', '大后天'];
    $date   = $dayArr[$day - $nowDay];

    return $date . \date('H:i:s', $time);

}

/**
 * 资金操作日志创建
 * @param  Arraty $data 日志数据
 * @return Boolen
 */
function bill($data)
{

    $bill = M('bill')->add($data);
    if (empty($bill)) return FALSE;

    return TRUE;
}

/**
 * 根据卡号获取银行名称,
 * 支持目前只支持招商
 * @return array or boolen
 */
function getBankName($account)
{

    $ubin = substr($account, 0, 6);
    $res  = M('bank')->find($ubin);
    if (empty($res) || $res['bc'] != 'CMBC') return NULL;

    return ['bc' => $res['bc'], 'name' => $res['name']];
}

/**
 * 检查客户端是否为手机
 * @return boolean
 */
function is_mobile()
{

    if (empty($_SERVER['HTTP_USER_AGENT'])) return FALSE;
    $user_agent    = strtolower($_SERVER['HTTP_USER_AGENT']);
    $mobile_agents = [
        "240x320", "acer", "acoon", "acs-", "abacho", "ahong", "airness", "alcatel", "amoi", "android", "anywhereyougo.com", "applewebkit/525", "applewebkit/532", "asus", "audio", "au-mic", "avantogo", "becker", "benq", "bilbo", "bird", "blackberry", "blazer", "bleu", "cdm-", "compal", "coolpad", "danger", "dbtel", "dopod", "elaine", "eric", "etouch", "fly ", "fly_", "fly-", "go.web", "goodaccess", "gradiente", "grundig", "haier", "hedy", "hitachi", "htc", "huawei", "hutchison", "inno", "ipad", "ipaq", "ipod", "jbrowser", "kddi", "kgt", "kwc", "lenovo", "lg ", "lg2", "lg3", "lg4", "lg5", "lg7", "lg8", "lg9", "lg-", "lge-", "lge9", "longcos", "maemo", "mercator", "meridian", "micromax", "midp", "mini", "mitsu", "mmm", "mmp", "mobi", "mot-", "moto", "nec-", "netfront", "newgen", "nexian", "nf-browser", "nintendo", "nitro", "nokia", "nook", "novarra", "obigo", "palm", "panasonic", "pantech", "philips", "phone", "pg-", "playstation", "pocket", "pt-", "qc-", "qtek", "rover", "sagem", "sama", "samu", "sanyo", "samsung", "sch-", "scooter", "sec-", "sendo", "sgh-", "sharp", "siemens", "sie-", "softbank", "sony", "spice", "sprint", "spv", "symbian", "tablet", "talkabout", "tcl-", "teleca", "telit", "tianyu", "tim-", "toshiba", "tsm", "up.browser", "utec", "utstar", "verykool", "virgin", "vk-", "voda", "voxtel", "vx", "wap", "wellco", "wig browser", "wii", "windows ce", "wireless", "xda", "xde", "zte",
    ];
    $is_mobile     = FALSE;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = TRUE;
            break;
        }
    }

    return $is_mobile;
}

/**
 * 检查客户端是否为微信或qq内置浏览器
 * @return boolean
 */
function is_wechat()
{

    //通过 hader user agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MicroMessenger') === FALSE && strpos($user_agent, 'QQ') === FALSE) return FALSE;

    //通过微信openid
    //该步骤等待微信公众接入再实现
    return TRUE;
}

/**
 * 根据 header user agent 检查客户端操作系统
 * @param  string $type 操作系统类型 默认为android
 * @return boolean
 */
function deveice($type = "android")
{

    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    switch ($type) {
        case 'android':
            return strpos($agent, 'android');
            break;
        case 'ios':
            return strpos($agent, 'iphone') || strpos($agent, 'ipad');
            break;

        default:
            return FALSE;
            break;
    }
}

/**
 * 设置用户消息
 * @param String $title 标题
 * @param String $type  用户端，user为快递人，usership为用户
 * @param String $msg   消息正文
 * @param String $uid   用户id
 */
function setUserMsg($title, $type, $msg, $uid)
{

    $data = [
        'title'   => $title,
        'type'    => $type,
        'content' => $msg,
        'uid'     => $uid,
    ];
    $res  = M('usermsg')->add($data);
    if (empty($res)) return FALSE;

    return TRUE;
}

/*无$orderid是完善资料，有$orderid完成订单，给usership用户加积分
 *$uid-usership的ID
 **/
function add_usership_score($uid, $mobile, $orderid)
{

    if ($orderid > 0) {
        $id = 13;
    } else {
        $id = 12;
    }
    $tscore_rule = M('Integral_rule')->where('id=' . $id)->find();
    $usership    = M('Usership')->where(['id' => $uid])->find();
    $tscore      = $tscore_rule['num'] + $usership['integral'];

    if (M('Usership')->where(['id' => $uid])->save(['integral' => $tscore])) {
        if ($orderid) {
            $info = '完成订单' . $orderid;
        } else {
            $info = '邀请的快递员注册并完善个人资料';
        }
        M('Integral_log')->add(['uid' => $uid, 'utype' => 'usership', 'info' => $info, 'score' => '+' . $tscore_rule['num'], 'action_id' => $id, 'ctime' => date('Y-m-d H:i:s')]);
        //if($id==13){//完成订单
        //发得积分的系统消息
        $uinfo    = M('User')->where(['mobile' => $mobile])->find();
        $nickname = M('Userinfo')->where('uid=' . $uinfo['id'])->getField('nickname');
        $nickname = $nickname == '' ? $uinfo['mobile'] : $nickname;
        $conts    = str_replace(['[username]', '[score]', '[orderid]'], [$nickname, $tscore_rule['num'], $orderid], $tscore_rule['description']);
        /*}else{//邀请
            $nickname=$nickname==''?$usership['mobile']:$usership['nickname'];
            $conts=str_replace(array('[username]','[score]','[orderid]'),array($nickname,$tscore_rule['num'],$orderid), $tscore_rule['description']);
        }*/
        //D('Usership')->add_msg_log($uid,'user',array('title'=>'系统消息','content'=>$conts));
        $data = [
            'uid'     => $uid,
            'type'    => 'user',
            'title'   => '系统消息',
            'content' => $conts,
            'ctime'   => date('Y-m-d H:i:s'),
        ];
        M('Usermsg')->add($data);
    }

    return TRUE;
}

/**
 *快递员接单，给用户返券
 * （urgen为下加急件单奖励优惠券额度，today为下今日达单奖励优惠券额度，max为最大额，发优惠券）
 * $user_id-邀请人，$uid-被邀请人，$order-订单信息
 */
function get_invite_vouch($user_id, $uid, $otype)
{

    $us_set = C('USER_INV_SENDER');//上级是下单用户（urgen为下加急件单奖励优惠券额度，today为下今日达单奖励优惠券额度，max为最大额，发优惠券）
    //var_dump($u_set,$us_set);exit;
    $user = M('Usership')->where(['id' => $user_id])->find();
    if ($user) {
        $coupon = M('Coupon')->field('sum(coupon) as coup')->where(['type' => 2, 'biaoshi' => 'qmks_invite1', 'user_id' => $user['id'], 'uid_type' => 'sender-' . $uid])->find();
        if ($coupon['coup'] < $us_set['max']) {
            //剩余额度
            $sy_quota = ($us_set['max'] - $coupon['coup']) * 1;
            if ($otype == 'today') {//本单为今日达
                if ($sy_quota >= $us_set['today']) {
                    $data = [
                        'type'        => 2,
                        'money'       => $us_set['today_limit'],
                        'coupon'      => $us_set['today'],
                        'biaoshi'     => 'qmks_invite1',
                        'start_time'  => time(),
                        'end_time'    => strtotime('1 years'),
                        'create_time' => time(),
                        'user_id'     => $user['id'],
                        'uid_type'    => 'sender-' . $uid,
                    ];
                    create_coupon($data);
                }
            } else {//本单为加急件
                if ($sy_quota >= $us_set['urgent']) {
                    $data = [
                        'type'        => 2,
                        'money'       => $us_set['urgent_limit'],
                        'coupon'      => $us_set['urgent'],
                        'biaoshi'     => 'qmks_invite1',
                        'start_time'  => time(),
                        'end_time'    => strtotime('1 years'),
                        'create_time' => time(),
                        'user_id'     => $user['id'],
                        'uid_type'    => 'sender-' . $uid,
                    ];
                    create_coupon($data);
                }
            }
        }
    }

    return TRUE;
}

/**
 *支付宝退款
 *$order_id-订单id，$trade_no-支付流水号,$money-退款金额
 */
function qmks_refund_alipay($order_id, $trade_no, $money)
{

    $alipay_key = C('ALIPAY_KEY');
    Vendor('alipay.AopSdk');
    $aop                     = new \AopClient();
    $aop->gatewayUrl         = 'https://openapi.alipay.com/gateway.do';
    $aop->appId              = '2017072507893092';
    $aop->rsaPrivateKey      = $alipay_key['private'];
    $aop->alipayrsaPublicKey = $alipay_key['public'];
    $aop->apiVersion         = '1.0';
    $aop->signType           = 'RSA2';
    $aop->postCharset        = 'UTF-8';
    $aop->format             = 'json';
    $request                 = new \AlipayTradeRefundRequest();
    $request->setBizContent('{"out_trade_no":"' . $order_id . '","trade_no":"' . $trade_no . '","refund_amount":"' . $money . '","refund_reason":"正常退款","out_request_no":"HZ01RF001","operator_id":"OP001","store_id":"NJ_S_001","terminal_id":"NJ_T_001"}');
    $result = $aop->execute($request);

    return $result;
}

/**
 * 导出数据到excel
 * @param       $list     数据列表[[key1=>data1,key2=>data2,...],[key1=>data1,key2=>data2,...],...]
 * @param       $filename 导出到excel文件名
 * @param array $header   表头[field1,field2,....]
 * @param array $index    字典key [key1,key2,...]
 */
function dumpExcel($list, $filename, $header = [], $index = [])
{

    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:filename=" . $filename . ".xls");
    $teble_header = implode("\t", $header);
    $strexport    = $teble_header . "\r";
    foreach ($list as $row) {
        foreach ($index as $val) {
            $strexport .= $row[$val] . "\t";
        }
        $strexport = rtrim($strexport) . "\r";
    }
    $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
    exit($strexport);
}

// 签名参数
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
 * @ideas 删除数组空值
 * @param array $arr 需去空数组
 * @return mixed 返回原数组
 */
function unsetEmptyArray($arr)
{

    foreach ($arr as $item => $value) {
        if (empty($value)) {
            unset($arr[$item]);
        }
    }

    return $arr;
}

/**
 * @ideas更新站点信息
 * @param       $where
 * @param array $data
 * @return array|bool|mixed|null|\PDOStatement|string|\think\Model
 */
function updateStationInfo($where, $data = [])
{

    $dbStation = M('station');
    /* 检测是否在当前应用注册 */
    $station_list = $dbStation->where($where)->find();
    if (NULL !== $station_list) {
        // 处理同一用户并发登录
        $res = $dbStation->where(['id' => $station_list['id']])->data($data)->save();
        if (FALSE !== $res) {

            return $station_list;
        }

        return FALSE;
    }

    return FALSE;
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
 * @ideas json格式返回
 * @param array  $data 数据
 * @param int    $code 状态码
 * @param string $msg  提示信息
 */
function jsonReturn($data = [], $code = 200, $msg = '成功')
{

    header('Content-type: application/json'); //设置json解析
    if ($code !== 200 && $msg === '成功') {
        $msg = '失败';
    }
    $param = [
        'code' => $code,
        'msg'  => $msg,
        'data' => $data,
    ];

    echo json_encode($param);
    exit;
}

/**
 * @ideas  极光推送(改)
 * @param  array $order 订单信息
 * @return bool
 */
function order_push($order)
{

    vendor('jgpush.autoload');
    $app_key       = '1fa91ac07b5520a48f2a249b';
    $master_secret = '6854c8e07d43bcbf2eafbbb9';
    $_id           = 0;
    if ($order['port'] !== 'user') {
        $_id = 1;
    }
    $range = C('SERVER_DISTANCE');
    if ($order['state'] < 3) {
        $coordinate = M('shipper')->where(['id' => $order['shipper_id']])->getField('take_grid');
    } else {
        $coordinates = M('order_details')->where(['order_id' => $order['order_id']])->field('lng,lat')->find();
        $coordinate  = $coordinates['lng'] . ',' . $coordinates['lat'];
    }
    $client = new \JPush\Client($app_key, $master_secret);
    $result = $client->push()->setPlatform('all')
                     ->addAllAudience()
                     ->message(
                         '新订单ID:' . $order['order_id'],
                         [
                             'title'        => '新订单',
                             'content_type' => 'text',
                             'extras'       => [
                                 'order_id'    => $order['order_id'],
                                 'coordinate'  => $coordinate,
                                 'range'       => $range,
                                 'notice_type' => $_id,
                             ],
                         ])->send();
    if ($result) {
        #return $result;
        return TRUE;
    }

    return FALSE;
}

/**
 * @ideas  坐标计算(返回范围内的坐标)
 * @param  string|float $lng      经度
 * @param  string|float $lat      纬度
 * @param  int          $distance 该点所在圆的半径，该圆与此正方形内切，默认值为5千米
 * @return array 正方形的四个点的经纬度坐标
 */
function returnSquarePoint($lng, $lat, $distance = 5)
{

    $dlng = 2 * asin(sin($distance / (2 * 6371)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);
    $dlat = $distance / 6371;
    $dlat = rad2deg($dlat);

    $point = [
        'left-top'     => ['lat' => $lat + $dlat, 'lng' => $lng - $dlng],
        'right-top'    => ['lat' => $lat + $dlat, 'lng' => $lng + $dlng],
        'left-bottom'  => ['lat' => $lat - $dlat, 'lng' => $lng - $dlng],
        'right-bottom' => ['lat' => $lat - $dlat, 'lng' => $lng + $dlng],
    ];

    return $point;
}

/**
 * @ideas  经纬度距离计算
 * @param  float $lon1   经度1
 * @param  float $lat1   纬度1
 * @param  float $lon2   经度2
 * @param  float $lat2   纬度2
 * @param  float $radius 地球半径
 * @return float|int
 */
function distance($lon1, $lat1, $lon2, $lat2, $radius = 6371.393)
{

    $rad   = floatval(M_PI / 180.0);
    $lat1  = floatval($lat1) * $rad;
    $lon1  = floatval($lon1) * $rad;
    $lat2  = floatval($lat2) * $rad;
    $lon2  = floatval($lon2) * $rad;
    $theta = $lon2 - $lon1;
    $dist  = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));
    if ($dist < 0) {
        $dist += M_PI;
    }
    $dist *= $radius * 1000;

    return round($dist);
}

/**
 * @ideas 验证手机号
 * @param $phone
 * @return bool|false|int
 */
function checkPhone($phone)
{

    $checkPhone = preg_match('/^(13|14|15|16|17|18|19)\d{9}$/', $phone);
    if ($checkPhone === 1) {
        return $checkPhone;
    }

    return FALSE;

}

/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return TRUE or FALSE
 */
function is_username($username)
{

    $strlen = mb_strlen($username);
    if (!preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z\x7f-\xff]+$/', $username)) {
        \jsonReturn([], 400, '名字格式错误【2-15个中英文】');
        #return FALSE;
    } else if (15 < $strlen || $strlen < 2) {
        \jsonReturn([], 400, '长度超不在范围内【2-15个中英文】');
        #return FALSE;
    }

    return TRUE;
}

/**
 * @ideas 查询站点信息
 * @param float  $lng   经度
 * @param float  $lat   纬度
 * @param int    $page  页数
 * @param int    $num   每页条数
 * @param int    $km    默认为5km
 * @param string $field 获取的字段
 * @return array|mixed|\PDOStatement|string|\think\Collection 返回排序好的站点由近到远
 */
function selectStation($lng, $lat, $page = 1, $num = 10, $km = 10, $field = 'id,name,person,phone,location')
{

    try {
        $dbStation    = M('station');
        $squares      = returnSquarePoint($lng, $lat, $km);//默认为5km
        $where['lng'] = [
            ['lt', $squares['right-bottom']['lng']],
            ['gt', $squares['left-top']['lng']],
        ];
        $where['lat'] = [
            ['gt', $squares['right-bottom']['lat']],
            ['lt', $squares['left-top']['lat']],
        ];
        $pages        = $dbStation->where($where)->count('id');
        $field        .= ',lng,lat';
        $list         = $dbStation->where($where)->page($page, $num)->field($field)->select();
        foreach ($list as $k => $item) {
            $lng2           = $item['lng'];
            $lat2           = $item['lat'];
            $list[$k]['km'] = distance($lng, $lat, $lng2, $lat2);
            $beeline[]      = $list[$k]['km'];
        }
        array_multisort($beeline, SORT_NUMERIC, $list);
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                if ($value['km'] > 1100) {
                    $value['km']      /= 1000;
                    $value['km']      = \round($value['km'], 2);
                    $list[$key]['km'] = $value['km'] . 'km';
                } else {
                    $list[$key]['km'] = $value['km'] . 'm';
                }
            }
        }
        $data['pages'] = ceil($pages / $num);
        $data['page']  = $page;
        $data['data']  = empty($list) ? [] : $list;

        return $data;
    } catch (\Exception $e) {
        printf($e->getMessage());
    }
}

/**
 * @desc  获取站点账号是否开工
 * @param $station_id
 * @return bool
 */
function getStationListen($station_id)
{

    $listen = M('station')->alias('a')->where(['a.id' => $station_id])
                          ->join('qmks_user AS b ON a.phone = b.mobile')
                          ->getField('b.is_listen');
    if ($listen === '1') {
        return TRUE;
    }

    return FALSE;
}

/**
 * @desc取出离坐标最近并且接单的站点id
 * @param      $lng
 * @param      $lat
 * @param int  $station_id
 * @param int  $page
 * @param int  $num
 * @param int  $km
 * @return null
 */
function judgeStation($lng, $lat, $station_id = NULL, $page = 1, $num = 10, $km = 100)
{

    try {
        $_id      = [];
        $stations = \selectStation($lng, $lat, $page, $num, $km, 'id,area_id')['data'];//查找范围内站点信息
        if (!empty($stations)) {
            foreach ($stations as $item) {
                //if ($item['id'] !== $station_id) {
                $end_flg = getStationListen($item['id']);//判断站点是否开工
                if (TRUE === $end_flg) {
                    $_id['id']      = $item['id'];
                    $_id['area_id'] = $item['area_id'];
                    break;
                }
                //}
            }
        }
        if (empty($_id)) {
            if ($page < 100) {
                \judgeStation($lng, $lat, $station_id, $page + 1);
            }
        }

        return $_id;
    } catch (\Exception $e) {
        printf($e->getMessage());
    }
}

/**
 * @desc连接redis
 * @return \Redis
 */
function redis()
{

    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);

    return $redis;
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
 * @ideas 接口限流
 * @param string $usb   接口名
 * @param int    $count 限流次数
 * @param string $type  计算时间时间类型( h ,m ,s)
 * @return int   返回队列长度
 */
function interfaceCurrentLimiting($usb = NULL, $count = 10, $type = 'm')
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
 * @desc多维变一维
 * @param array $arr 多维数组
 * @return array|bool
 */
function arrToOne(array &$arr)
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

    return $arr = $tmp;
}

/**
 * @desc多维变一维
 * @param $arr
 * @return array
 */
function arrMoreToOne(array &$arr)
{

    $result = [];
    array_walk_recursive($arr, function ($value) use (&$result) {

        $result[] = $value;
    });

    return $arr = $result;
}

/**
 * @desc 处理订单任务中的距离问题
 * @param $arr
 * @return mixed
 */
function m_convert_km($arr)
{

    foreach ($arr as $item => $value) {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                if (\is_array($v)) {
                    $flg = is_numeric($v['km']);
                    if (TRUE === $flg) {
                        if ($v['km'] > 1200) {
                            $arr[$item][$k]['km'] /= 1000;
                            $arr[$item][$k]['km'] = \round($arr[$item][$k]['km'], 2);
                            $arr[$item][$k]['km'] .= 'km';
                        } else {
                            $arr[$item][$k]['km'] .= 'm';
                        }
                    }
                }
            }
        }
    }

    return $arr;
}

/**
 * @desc判断是否为多维数组(返回数组个数)
 * @param $arr
 * @return int
 */
function getmaxdim($arr)
{

    if (!is_array($arr)) {
        return 0;
    } else {
        $max1 = 0;
        foreach ($arr as $item1) {
            $t1 = getmaxdim($item1);
            if ($t1 > $max1) {
                $max1 = $t1;
            }
        }

        return $max1 + 1;
    }
}

/**
 * @desc整数转换为字符串
 * @param $arr
 * @param $field
 * @return mixed
 */
function intConvertString(&$arr, $field)
{

    array_walk_recursive($arr, function (&$value, $key) use ($field) {

        if ($key === $field) {
            $value = (string)$value;
        }
    });

    return $arr;
}

/**
 * @param   string $url 网址
 * @param   bool   $ssl 是否为https
 * @return  bool|mixed  返回数据
 */
function curlGet($url, $ssl = TRUE)
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
function curlPost($url, $data, $ssl = TRUE)
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

//把json字符串转数组
function json_to_array($p)
{

    if (mb_detect_encoding($p, ['ASCII', 'UTF-8', 'GB2312', 'GBK']) != 'UTF-8') {
        $p = iconv('GBK', 'UTF-8', $p);
    }

    return json_decode($p, TRUE);
}

function array_group_by($arr, $key)
{

    $grouped = [];
    foreach ($arr as $value) {
        $grouped[$value[$key]][] = $value;
    }
    if (func_num_args() > 2) {
        $args = func_get_args();
        foreach ($grouped as $key => $value) {
            $parms         = array_merge([$value], array_slice($args, 2, func_num_args()));
            $grouped[$key] = call_user_func_array('array_group_by', $parms);
        }
    }

    return $grouped;
}

/**
 * @desc 中文字符串去重
 * @param $string
 * @return array|array[]|false|string|string[]
 */
function strUnique(&$string)
{

    $string = preg_split('/(?<!^)(?!$)/u', $string);
    $string = array_unique($string);
    $string = implode('', $string);
    $len    = mb_stripos($string, '省');
    if ($len) {
        $start    = mb_stripos($string, '区');
        $province = mb_substr($string, $start + 1, $len - $start);
        $string   = $province . str_replace($province, '', $string);
    }

    return $string;
}

/**
 * @desc 获取百度地图坐标
 * @param string $address 地址信息 例：北京市海淀区上地十街10号
 * @return array
 */
function getBaiduCoordinate($address)
{

    $url     = 'http://api.map.baidu.com/geocoder/v2/?address=' . $address . '&output=json&ak=X4ypmQvhe9Uh3Eb8iVfID8g5tkxcMyBs';
    $address = \curlGet($url, FALSE);
    $address = \json_to_array($address);
    if ($address['status'] === 0) {
        return [
            'lng' => $address['result']['location']['lng'],
            'lat' => $address['result']['location']['lat'],
        ];
    } else {
        \jsonReturn([], -5, '地址错误,例：北京市海淀区上地十街10号');
    }
}

/**
 * @desc 获取百度地图位置
 * @param string $lat 纬度
 * @param string $lng 经度
 * @return mixed
 */
function getBaiduAddress($lng, $lat)
{

    $url_ = "http://api.map.baidu.com/geocoder/v2/?location={$lat},{$lng}&output=json&pois=1&ak=X4ypmQvhe9Uh3Eb8iVfID8g5tkxcMyBs&radius=10&latest_admin=1";
    $res  = \curlGet($url_, FALSE);
    $res  = \json_to_array($res);
    if ($res['status'] === 0) {
        //\jsonReturn($res);
        return [
            'city'          => $res['result']['addressComponent']['city'],
            'district'      => $res['result']['addressComponent']['district'],
            'street'        => $res['result']['addressComponent']['street'],
            'street_number' => $res['result']['addressComponent']['street_number'],
            'address'       => $res['result']['formatted_address'],
        ];
    } else {
        \jsonReturn([], -6, '地址错误,例：北京市海淀区上地十街10号');
    }
}

/**
 * @desc坐标转换
 * @param  array  $coordinate     坐标
 * @param  string $coordinateType 坐标类型
 * @param string  $toType         要转换的类型
 * @return null|string
 *
 * form  源坐标类型：
 * 1：GPS设备获取的角度坐标，wgs84坐标;
 * 2：GPS获取的米制坐标、sogou地图所用坐标;
 * 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标，国测局（gcj02）坐标;
 * 4：3中列表地图坐标对应的米制坐标;
 * 5：百度地图采用的经纬度坐标;
 * 6：百度地图采用的米制坐标;
 * 7：mapbar地图坐标;
 * 8：51地图坐标
 *
 * to  目标坐标类型：
 * 3：国测局（gcj02）坐标;
 * 4：3中对应的米制坐标;
 * 5：bd09ll(百度经纬度坐标);
 * 6：bd09mc(百度米制经纬度坐标)
 */
function coordinateInterconversion(array $coordinate = [], $coordinateType = NULL, $toType = NULL)
{

    $bd_coordinates  = $coordinate['lng'] . ',' . $coordinate['lat'];//坐标
    $tx_coordinates  = $coordinate['lat'] . ',' . $coordinate['lng'];//坐标
    $coordinateTypes = [
        1 => 'wgs84',
        3 => 'gcj02',
        5 => 'baidu',
    ];
    $toTypes         = ['Tencent', 'baidu'];
    $key             = array_search($coordinateType, $coordinateTypes, TRUE);
    if ($key !== FALSE) {
        switch ($coordinateType) {
            case'wgs84':
            case'gcj02':
                //腾讯转换为百度坐标
                $from = $key;
                $to   = 5;
                $ak   = 'X4ypmQvhe9Uh3Eb8iVfID8g5tkxcMyBs';
                $url  = 'http://api.map.baidu.com/geoconv/v1/?coords=' . $bd_coordinates . '&from=' . $from . '&to=' . $to . '&ak=' . $ak;
                $res  = \curlGet($url, FALSE);
                $res  = \json_to_array($res);
                if ($res['status'] === 0) {
                    $coordinate = $res['result'][0]['x'] . ',' . $res['result'][0]['y'];
                } else {
                    $coordinate = $bd_coordinates;
                }
                break;
            default:
                $type = 3;//输入的locations的坐标类型
                $key  = 'LCABZ-VKBKO-UGQW4-SHK3V-OZSDS-3MFUZ';
                $url  = 'https://apis.map.qq.com/ws/coord/v1/translate?key=' . $key . '&locations=' . $tx_coordinates . '&type=' . $type;
                $res  = \curlGet($url, TRUE);
                $res  = \json_to_array($res);
                if ($res['status'] === 0) {
                    $coordinate = $res['locations'][0]['lng'] . ',' . $res['locations'][0]['lat'];
                } else {
                    $coordinate = $bd_coordinates;
                }
                break;
        }
    }

    return $coordinate;
}

/**
 * @desc返还快递员抵押金
 * @param float $money 释放金额
 * @param int   $uid   快递员ID
 * @return bool
 */
function returnDeposit($money, $uid)
{

    $undeposit = round($money, 2);
    $res       = M('user')->where(['id' => $uid])->setDec('deposit', $undeposit);
    if (FALSE === $res) {
        return FALSE;
    }

    return TRUE;
}

/**
 * @desc获取小程序openid
 * @param $code
 * @return bool
 */
function getOpenId($code)
{

    $url    = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx265553e1b2723f7a&secret=8e81f8c6ddb0b7e17e6e28351f81b7fd&js_code=' . $code . '&grant_type=authorization_code';
    $result = \curlGet($url);
    $result = \json_to_array($result);
    if ($result['openid']) {
        $result['code'] = $code;

        return $result;
    } else {
        jsonReturn([], 500, $result['errmsg']);
    }

    return FALSE;
}

/**
 * @desc加载支付必须文件
 */
function wx_require_once()
{

    ini_set('date.timezone', 'Asia/Shanghai');
    require_once APP_ROOT . '/ThinkPHP/Library/Vendor/wxpay/lib/WxPay.Api.php';
    require_once APP_ROOT . '/ThinkPHP/Library/Vendor/wxpay/example/WxPay.JsApiPay.php';
    require_once APP_ROOT . '/ThinkPHP/Library/Vendor/wxpay/example/log.php';
    //初始化日志
    $logHandler = new CLogFileHandler(APP_ROOT . '/ThinkPHP/Library/Vendor/wxpay/logs/' . date('Y-m-d') . '.log');
    Log::Init($logHandler, 15);

    return;
}

/**
 * 小程序支付
 * @param string $openId     openid
 * @param string $goods      商品名称
 * @param string $order_sn   订单号
 * @param int    $total_fee  金额
 * @param string $notify_url 回调地址
 * @param string $attach     附加参数,我们可以选择传递一个参数,比如订单ID
 * @return mixed
 * @throws \WxPayException
 */
function wxpay($openId, $goods, $order_sn, $total_fee, $notify_url = '', $attach = '')
{

    $goods      = empty($goods) ? '全员快送支付' : $goods;
    $notify_url = empty($notify_url) ? 'http://api.qykuaisong.com/Home/Expay/wxpay_notify_new.html' : $notify_url;
    wx_require_once();
    $tools = new JsApiPay();
    if (empty($openId)) $openId = $tools->GetOpenid();
    $input = new WxPayUnifiedOrder();
    $input->SetBody($goods);     //商品名称
    $input->SetAttach($attach);     //附加参数,可填可不填,填写的话,里边字符串不能出现空格
    $input->SetOut_trade_no($order_sn);   //订单号
    $input->SetTotal_fee($total_fee);   //支付金额,单位:分
    $input->SetTime_start(date('YmdHis'));  //支付发起时间
    $input->SetTime_expire(date('YmdHis', time() + 600));//支付超时
    $input->SetGoods_tag('全员快送');#设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
    $input->SetNotify_url($notify_url);
    $input->SetTrade_type('JSAPI');    //支付类型
    $input->SetOpenid($openId);     //用户openID
    $order  = WxPayApi::unifiedOrder($input); //统一下单
    $result = $tools->GetJsApiParameters($order);
    file_put_contents('wx/result.txt', $result . PHP_EOL, FILE_APPEND);
    file_put_contents('wx/order.txt', json_encode($order) . PHP_EOL, FILE_APPEND);

    return json_to_array($result);
}

/**
 * @desc把json字符串转数组
 * @param $json
 * @return mixed
 */
function jsonConversionArray(&$json)
{

    if (is_array($json)) {
        $jsonCopy = $json;
        $json     = [];
        array_walk_recursive($jsonCopy, function (&$value, $key) use (&$json) {

            if (mb_detect_encoding($value, ['ASCII', 'UTF-8', 'GB2312', 'GBK']) !== 'UTF-8') {
                $value = iconv('GBK', 'UTF-8', $value);
            }
            $json[] = json_decode($value, TRUE);
        });
    } else {
        if (mb_detect_encoding($json, ['ASCII', 'UTF-8', 'GB2312', 'GBK']) !== 'UTF-8') {
            $json = iconv('GBK', 'UTF-8', $json);
        }
        $json = json_decode($json, TRUE);
    }

    return $json;
}

/**
 * @desc 收集微信小程序from_id
 * @param   string $from_id
 * @param   int    $uid
 * @param   string $order_id
 * @param   string $type
 * @return  bool
 */
function saveFromId($from_id, $uid, $order_id, $type = 'submit')
{

    $from_id_db  = M('from_id');
    $create_time = time();
    $expire_time = \strtotime('+7 days', $create_time);
    if ($type === 'submit') {
        $num = 1;
    } else if ($type === 'pay') {
        $num = 3;
    }
    $from_id_data = [
        'from_id'     => $from_id,
        'uid'         => $uid,
        'create_time' => $create_time,#收集时间
        'expire_time' => $expire_time,#过期时间(7天内有效)
        #可使用次数(1次支付可下发3条，多次支付下发条数独立，互相不影响)
        #（1次提交表单可下发1条，多次提交下发条数独立，相互不影响）
        'can_use_num' => $num,
        'order_id'    => $order_id,#过期时间
    ];
    $id           = $from_id_db->data($from_id_data)->add();
    if ($id) {
        return TRUE;
    }

    return FALSE;
}

/**
 * @desc获取用户的from_id
 * @param    $uid
 * @return   mixed
 */
function getFromId($uid)
{

    $from_id_db = M('from_id');
    $where      = [
        'uid'         => $uid,
        'expire_time' => ['EGT', time()],#有效期内
        'can_use_num' => ['GT', 0],#可用次数大于0
    ];
    if (empty($uid)) {
        unset($where['uid']);
    }

    return $from_id_db->where($where)->getField('from_id');
}

/**
 * @desc减少from_id可用次数
 * @param   $from_id
 * @return  bool|int|true
 * @throws  \think\Exception
 */
function setDecFromId($from_id)
{

    $from_id_db = M('from_id');

    return $from_id_db->where(['from_id' => $from_id])->setDec('can_use_num');
}

/**
 * @desc收集微信小程序openid
 * @param   int    $uid
 * @param   string $code
 * @param   string $openid
 * @param   string $session_key
 * @param   string $expire_time
 * @return  bool
 */
function saveOpenId($uid, $code, $openid, $session_key, $expire_time = '+7 days')
{

    $open_id_db   = M('open_id');
    $create_time  = time();
    $expire_times = \strtotime($expire_time, $create_time);#过期时间(默认七天)
    $open_id_data = [
        'uid'         => $uid,
        'code'        => $code,
        'openid'      => $openid,
        'session_key' => $session_key,
        'expire_time' => $expire_times,#过期时间
    ];
    if (!$open_id_db->where(['uid' => $uid])->find()) {
        $id = $open_id_db->data($open_id_data)->add();
        if ($id) {
            return TRUE;
        }

        return FALSE;
    }

    return TRUE;
}

/**
 * @desc获取用户的open_id
 * @param    $uid
 * @return   mixed
 */
function getUserOpenId($uid)
{

    $open_id_db = M('open_id');
    $where      = [
        'uid' => $uid,
        #'expire_time' => ['EGT', time()],#有效期内
    ];

    return $open_id_db->where($where)->getField('openid');
}

/**
 * @param   string $table 表名
 * @param   array  $where 查询条件
 * @param   string $field 查询字段
 * @param   string $order 字段排序规则
 * @param   int    $num   分页显示数目
 * @return  array        返回分页和查询数据
 */
function page($table = NULL, $where = [], $field = '', $order = 'id desc', $num = 10)
{

    try {
        $count = M($table)->where($where)->count();// 查询满足要求的总记录数
        $Page  = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数(5)
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
 * @desc   隐藏字符串
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @param  null   $str
 * @param  string $start
 * @param  string $len
 * @param  string $icon
 * @return mixed
 */
function hideStr(&$str = NULL, $start = 3, $len = 4, $icon = '*')
{

    if ($str === NULL) {
        return $str;
    }
    $replacement = str_repeat($icon, $len);//把字符串 $icon 重复 $len次

    return $str = substr_replace($str, $replacement, $start, $len);
}

/**
 * @desc删除文件
 * @param string $file 文件名（路径+文件名） 格式 /upload/portal/20180907/7067721951bed99f64e9415f2a5eb496.jpg
 * @return bool
 */
function unlinkFlie($file)
{

    $end       = strrpos($file, '/');
    $file_path = \substr($file, 0, $end);
    $file_name = substr($file, $end + 1);
    if (is_dir($file_path)) {
        $files = $file_path . '/' . $file_name;
        if (is_file($files)) {
            unlink($files);

            return TRUE;
        }

        return FALSE;
    }

    return FALSE;
}

function get_server_status()
{

    $fp = popen('top -b -n 2 | grep -E "^(Cpu|Mem|Tasks)"', "r");//获取某一时刻系统cpu和内存使用情况
    $rs = "";
    while (!feof($fp)) {
        $rs .= fread($fp, 1024);
    }
    pclose($fp);
    $sys_info = explode("\n", $rs);

    $tast_info = explode(",", $sys_info[3]);//进程 数组
    $cpu_info  = explode(",", $sys_info[4]); //CPU占有量 数组
    $mem_info  = explode(",", $sys_info[5]); //内存占有量 数组

    //正在运行的进程数
    $tast_running = trim(trim($tast_info[1], 'running'));
    //CPU占有量
    $cpu_usage = trim(trim($cpu_info[0], 'Cpu(s): '), '%us'); //百分比

    //内存占有量
    $mem_total = trim(trim($mem_info[0], 'Mem: '), 'k total');
    $mem_used  = trim($mem_info[1], 'k used');
    $mem_usage = round(100 * intval($mem_used) / intval($mem_total), 2); //百分比

    /*硬盘使用率 begin*/
    $fp = popen('df -lh | grep -E "^(/)"', "r");
    $rs = fread($fp, 1024);
    pclose($fp);
    $rs       = preg_replace("/\s{2,}/", ' ', $rs); //把多个空格换成 “_”
    $hd       = explode(" ", $rs);
    $hd_avail = trim($hd[3], 'G'); //磁盘可用空间大小 单位G
    $hd_usage = trim($hd[4], '%'); //挂载点 百分比
    /*硬盘使用率 end*/

    //检测时间
    $fp = popen("date +\"%Y-%m-%d %H:%M\"", "r");
    $rs = fread($fp, 1024);
    pclose($fp);
    $detection_time = trim($rs);

    return [
        'cpu_usage'      => $cpu_usage,
        'mem_usage'      => $mem_usage,
        'hd_avail'       => $hd_avail,
        'hd_usage'       => $hd_usage,
        'tast_running'   => $tast_running,
        'detection_time' => $detection_time,
    ];
}

