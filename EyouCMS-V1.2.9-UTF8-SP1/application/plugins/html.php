<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */


$html_cache_arr = array();
// 全局变量数组
$global = tpCache('global');
// 系统模式
$web_cmsmode = isset($global['web_cmsmode']) ? $global['web_cmsmode'] : 2;
/*页面缓存有效期*/
$app_debug = true;
$web_htmlcache_expires_in = -1;
if (1 == $web_cmsmode) { // 运营模式
    $app_debug = false;
    $web_htmlcache_expires_in = isset($global['web_htmlcache_expires_in']) ? $global['web_htmlcache_expires_in'] : 7200;
}
/*--end*/

/*缓存的页面*/
$html_cache_arr = array();
/*--end*/

/*引入全部插件的页面缓存规则*/
$html_list = glob(WEAPP_DIR_NAME.DS.'*'.DS.'html.php');
if (!empty($html_list)) {
    foreach ($html_list as $key => $file) {
        $html_value = include_once $file;
        if (empty($html_value)) {
            continue;
        }
        foreach ($html_value as $k => $v) {
            if (!empty($v) && is_array($v)) {
                $v = array_merge($v, array('cache'=>$web_htmlcache_expires_in));
                $html_value[$k] = $v;
            }
        }
        $html_cache_arr = array_merge($html_value, $html_cache_arr);
    }
}
/*--end*/

return array(
    // 应用调试模式
    'app_debug' => $app_debug,
    // 模板设置
    'template' => array(
        // 模板路径
        'view_path' => './template/plugins/',
        // 模板后缀
        'view_suffix' => 'htm',
    ),
    // 视图输出字符串内容替换
    'view_replace_str' => array(),

    /**假设这个访问地址是 www.xxxxx.dev/home/goods/goodsInfo/id/1.html 
     *就保存名字为 index_goods_goodsinfo_1.html     
     *配置成这样, 指定 模块 控制器 方法名 参数名
     */
    'HTML_CACHE_ARR'=> $html_cache_arr,
);
?>