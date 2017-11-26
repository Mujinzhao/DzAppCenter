<?php

/*
 *  $ Discuz! Frame v0.1  (C) 2011-2012
 *  $ config.php
 *  $ Discuz! Frame 的基础配置文件
 *  + 2011-10-06  创建文件
 */

$_config = array();

/**
 * DEBUG 信息设置
 * @example
 * $config['debug'] = 1 ->标准模式
 * $config['debug'] = 2 -> E_ALL模式
 * $config['debug'] = 字符串 -> 当前 $_GET、$_POST 等 REQUEST 参数中包含 debug=字串 时显示
 */
$_config['debug'] = 1;

//Sae模式
$_config['Sae'] = '1'; //0 = 关闭, 1 = 开启;
// ----------------------------  CONFIG DB  ----------------------------- //
//  CONFIG CACHE
$_config['cache']['model'] = '1'; //1=普通模式, 2=SAE模式
$_config['cache']['type'] = 'sql'; // 缓存类型 file=文件缓存, sql=数据库缓存
$_config['STYLEID'] = '1'; //默认风格ID
// ---------------------------  CONFIG CACHE  --------------------------- //
$_config['cache']['type'] = 'sql';

// --------------------------  CONFIG MEMORY  --------------------------- //
$_config['memory']['prefix'] = 'x0T_';
$_config['memory']['apc'] = 1;
$_config['memory']['memcache']['server'] = '';
$_config['memory']['memcache']['port'] = 11211;
$_config['memory']['memcache']['pconnect'] = 1;
$_config['memory']['memcache']['timeout'] = 1;

// --------------------------  CONFIG COOKIE  --------------------------- //
$_config['cookie']['cookiepre'] = 'x0T_';
$_config['cookie']['cookiedomain'] = '';
$_config['cookie']['cookiepath'] = '/';

// 页面输出设置
$_config['output']['charset'] = 'utf-8'; // 页面字符集
$_config['output']['forceheader'] = 1;  // 强制输出页面字符集，用于避免某些环境乱码
$_config['output']['gzip'] = 0;  // 是否采用 Gzip 压缩输出
$_config['output']['tplrefresh'] = 1;  // 模板自动刷新开关 0=关闭, 1=打开
$_config['output']['language'] = 'zh_cn'; // 页面语言 zh_cn/zh_tw
$_config['output']['staticurl'] = 'static/'; // 站点静态文件路径，“/”结尾
$_config['output']['ajaxvalidate'] = 0;  // 是否严格验证 Ajax 页面的真实性 0=关闭，1=打开
$_config['output']['iecompatible'] = 0;  // 页面 IE 兼容模式
// ----------------------------  数据库相关设置---------------------------- //
$_config['db']['1']['dbhost'] = '127.0.0.1:9988';
$_config['db']['1']['dbuser'] = 'root';
$_config['db']['1']['dbpw'] = 'root';
$_config['db']['1']['dbcharset'] = 'utf8';
$_config['db']['1']['dbname'] = 'dz_app_center';
$_config['db']['1']['tablepre'] = 'ac_';
$_config['debug'] = 0;
$_config['memory']['apc'] = 0;

$_config['db']['sae']['dbhost'] = SAE_MYSQL_HOST_M . ':' . SAE_MYSQL_PORT;
$_config['db']['sae']['dbuser'] = SAE_MYSQL_USER;
$_config['db']['sae']['dbpw'] = SAE_MYSQL_PASS;
$_config['db']['sae']['dbcharset'] = 'utf8';
$_config['db']['sae']['dbname'] = SAE_MYSQL_DB;
$_config['db']['sae']['tablepre'] = 'cdb_';
$_config['debug'] = 0;
$_config['memory']['apc'] = 0;


