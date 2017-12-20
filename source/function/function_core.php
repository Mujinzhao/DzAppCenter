<?php

/*
 *  $ Discuz! Frame v0.1  (C) 2011-2012
 *  $ function_core.php
 *  + 2011-10-07  创建文件
 *  + 2011-10-07  基本架构
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

define('DISCUZ_CORE_FUNCTION', true);

function __autoload($class_name) {
    require_once DISCUZ_ROOT . './source/class/class_' . $class_name . '.php';
}

function system_error($message, $show = true, $save = true, $halt = true) {
    require_once libfile('class/error');
    discuz_error::system_error($message, $show, $save, $halt);
}

function daddslashes($string, $force = 1) {
    if (is_array($string)) {
        $keys = array_keys($string);
        foreach ($keys as $key) {
            $val = $string[$key];
            unset($string[$key]);
            $string[addslashes($key)] = daddslashes($val, $force);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}

function dmicrotime() {
    return array_sum(explode(' ', microtime()));
}

function setglobal($key, $value, $group = null) {
    global $_G;
    $k = explode('/', $group === null ? $key : $group . '/' . $key);
    switch (count($k)) {
        case 1: $_G[$k[0]] = $value;
            break;
        case 2: $_G[$k[0]][$k[1]] = $value;
            break;
        case 3: $_G[$k[0]][$k[1]][$k[2]] = $value;
            break;
        case 4: $_G[$k[0]][$k[1]][$k[2]][$k[3]] = $value;
            break;
        case 5: $_G[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]] = $value;
            break;
    }
    return true;
}

function getglobal($key, $group = null) {
    global $_G;
    $k = explode('/', $group === null ? $key : $group . '/' . $key);
    switch (count($k)) {
        case 1: return isset($_G[$k[0]]) ? $_G[$k[0]] : null;
            break;
        case 2: return isset($_G[$k[0]][$k[1]]) ? $_G[$k[0]][$k[1]] : null;
            break;
        case 3: return isset($_G[$k[0]][$k[1]][$k[2]]) ? $_G[$k[0]][$k[1]][$k[2]] : null;
            break;
        case 4: return isset($_G[$k[0]][$k[1]][$k[2]][$k[3]]) ? $_G[$k[0]][$k[1]][$k[2]][$k[3]] : null;
            break;
        case 5: return isset($_G[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]]) ? $_G[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]] : null;
            break;
    }
    return null;
}

/**
 * 取出 get, post, cookie 当中的某个变量
 *
 * @param string $k  key 值
 * @param string $type 类型
 * @return mix
 */
function getgpc($k, $type = 'GP') {
    $type = strtoupper($type);
    switch ($type) {
        case 'G': $var = &$_GET;
            break;
        case 'P': $var = &$_POST;
            break;
        case 'C': $var = &$_COOKIE;
            break;
        default:
            if (isset($_GET[$k])) {
                $var = &$_GET;
            } else {
                $var = &$_POST;
            }
            break;
    }

    return isset($var[$k]) ? $var[$k] : NULL;
}

function authcode($string, $operation = 'DECODE', $key = 'Sa1On', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : getglobal('authkey'));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 远程文件文件请求兼容函数
 */
function dfsockopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
    require_once libfile('function/filesock');
    return _dfsockopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block);
}

/**
 * HTML转义字符
 * @param $string - 字符串
 * @return 返回转义好的字符串
 */
function dhtmlspecialchars($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val);
        }
    } else {
        $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
    }
    return $string;
}

function dexit($message = '') {
    echo $message;
    output();
    exit();
}

function dheader($string, $replace = true, $http_response_code = 0) {
    $string = str_replace(array("\r", "\n"), array('', ''), $string);
    if (empty($http_response_code) || PHP_VERSION < '4.3') {
        @header($string, $replace);
    } else {
        @header($string, $replace, $http_response_code);
    }
    if (preg_match('/^\s*location:/is', $string)) {
        exit();
    }
}

function getuserbyuid($uid) {
    static $users = array();
    if (empty($users[$uid])) {
        $users[$uid] = DB::fetch_first("SELECT * FROM " . DB::table('common_member') . " WHERE uid='$uid'");
    }
    return $users[$uid];
}

function getuserbyuin($uin) {
    static $users = array();
    if (empty($users[$uin])) {
        $users[$uin] = DB::fetch_first("SELECT * FROM " . DB::table('common_member') . " WHERE uin = '$uin'");
    }
    return $users[$uin];
}

/**
 * 设置cookie
 * @param $var - 变量名
 * @param $value - 变量值
 * @param $life - 生命期
 * @param $prefix - 前缀
 */
function dsetcookie($var, $value = '', $life = 0, $prefix = 1, $httponly = false) {

    global $_G;

    $config = $_G['config']['cookie'];

    $_G['cookie'][$var] = $value;
    $var = ($prefix ? $config['cookiepre'] : '') . $var;
    $_COOKIE[$var] = $value;

    if ($value == '' || $life < 0) {
        $value = '';
        $life = -1;
    }

    $life = $life > 0 ? getglobal('timestamp') + $life : ($life < 0 ? getglobal('timestamp') - 31536000 : 0);
    $path = $httponly && PHP_VERSION < '5.2.0' ? $config['cookiepath'] . '; HttpOnly' : $config['cookiepath'];

    $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
    if (PHP_VERSION < '5.2.0') {
        setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure);
    } else {
        setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure, $httponly);
    }
}

function getcookie($key) {
    global $_G;
    return isset($_G['cookie'][$key]) ? $_G['cookie'][$key] : '';
}

function clearcookies() {
    global $_G;
    foreach ($_G['cookie'] as $k => $v) {
        if ($k != 'widthauto') {
            dsetcookie($k, '', '-1');
        }
    }
    $_G['uid'] = $_G['adminid'] = 0;
    $_G['username'] = $_G['member']['password'] = '';
}

function fileext($filename) {
    return addslashes(trim(substr(strrchr($filename, '.'), 1, 10)));
}

//note 规则待调整
function formhash($specialadd = '') {
    $hashadd = 'SaIOn.BbSaPP.paY';
    return substr(md5(substr(TIMESTAMP, 0, -7) . $hashadd . getglobal('uid', 'member') . 'A-b-c-D-E-f-' . getglobal('username', 'member') . $GLOBALS['_G']['authkey'] . $specialadd), 8, 6);
}

function addhash($param, $key = '5b64bc51fc7527bbce9b2745f970eef7') {

    ksort($param); // $param 为待校验的所有参数
    $params = '';
    foreach ($param as $k => $v) {
        $params .= '&' . $k . '=' . rawurlencode($v);
    }
    $md5hash = md5(substr($params, 1) . $key);
    return $md5hash;
}

function addmsg($param) {

    ksort($param); // $param 为待校验的所有参数
    $params = '';
    foreach ($param as $k => $v) {
        $params .= '|' . $k . '=' . $v;
    }
    $return = substr($params, 1);
    return $return;
}

function addhashurl($param, $key = '5b64bc51fc7527bbce9b2745f970eef7') {
    ksort($param);
    $params = '';
    foreach ($param as $k => $v) {
        $params .= '&' . $k . '=' . rawurlencode($v);
    }

    $params .= '&md5hash=' . md5(substr($params, 1) . $key);
    $apiparams = substr($params, 1);
    $return = "http://addon.discuz.com/api/paynotify?$apiparams";
    return $return;
}

function dstrlen($str) {
    if (strtolower('UTF-8') != 'utf-8') {
        return strlen($str);
    }
    $count = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $value = ord($str[$i]);
        if ($value > 127) {
            $count++;
            if ($value >= 192 && $value <= 223)
                $i++;
            elseif ($value >= 224 && $value <= 239)
                $i = $i + 2;
            elseif ($value >= 240 && $value <= 247)
                $i = $i + 3;
        }
        $count++;
    }
    return $count;
}

function checkrobot($useragent = '') {
    static $kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
    static $kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';

    $useragent = empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent;

    if (!strexists($useragent, 'http://') && preg_match("/($kw_browsers)/i", $useragent)) {
        return false;
    } elseif (preg_match("/($kw_spiders)/i", $useragent)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 检查邮箱是否有效
 * @param $email 要检查的邮箱
 * @param 返回结果
 */
function isemail($email) {
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 产生随机码
 * @param $length - 长度
 * @param $numberic - 数值或字母
 * @return 返回字符串
 */
function random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

/**
 * 产生随机KEY
 * @param $length - 长度
 * @param $numberic - 数值或字母
 * @return 返回字符串
 */
function dkey($length, $type = '1') {
    if ($type == '1') {
        $seed = 'QWERTYUIPASDFGHJKLZXCVBNM123456789';
    } elseif ($type == '2') {
        $seed = 'QETUADGHKZCBM13579';
    } elseif ($type == '3') {
        $seed = 'WRYIPSFHKXVN2468';
    }
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

// 生成KEY
function cdkey($val) {
    return $val . dkey(1, 2) . dkey(1, 3) . '-' . dkey(4) . '-' . dkey(2) . dkey(1, 2) . dkey(1) . '-' . dkey(2) . dkey(1, 3) . dkey(1);
}

/**
 * 判断一个字符串是否在另一个字符串中存在
 *
 * @param string 原始字串 $string
 * @param string 查找 $find
 * @return boolean
 */
function strexists($string, $find) {
    return !(strpos($string, $find) === FALSE);
}

/**
 * 加载语言
 * 语言文件统一为 $lang = array();
 * @param $file - 语言文件，可包含路径如 forum/xxx home/xxx
 * @param $langvar - 语言文字索引
 * @param $vars - 变量替换数组
 * @return 语言文字
 */
function lang($file, $langvar = null, $vars = array(), $default = null) {
    global $_G;
    list($path, $file) = explode('/', $file);
    if (!$file) {
        $file = $path;
        $path = '';
    }


    $key = $path == '' ? $file : $path . '_' . $file;
    if (!isset($_G['lang'][$key])) {
        include DISCUZ_ROOT . './source/language/' . ($path == '' ? '' : $path . '/') . 'lang_' . $file . '.php';
        $_G['lang'][$key] = $lang;
    }
    $returnvalue = &$_G['lang'];

    $return = $langvar !== null ? (isset($returnvalue[$key][$langvar]) ? $returnvalue[$key][$langvar] : null) : $returnvalue[$key];
    $return = $return === null ? ($default !== null ? $default : $langvar) : $return;
    if ($vars && is_array($vars)) {
        $searchs = $replaces = array();
        foreach ($vars as $k => $v) {
            $searchs[] = '{' . $k . '}';
            $replaces[] = $v;
        }
        $return = str_replace($searchs, $replaces, $return);
    }
    return $return;
}

/**
 * 读取缓存
 * @param $cachenames - 缓存名称数组或字串
 */
function loadcache($cachenames, $force = false) {
    global $_G;
    static $loadedcache = array();
    $cachenames = is_array($cachenames) ? $cachenames : array($cachenames);
    $caches = array();
    foreach ($cachenames as $k) {
        if (!isset($loadedcache[$k]) || $force) {
            $caches[] = $k;
            $loadedcache[$k] = true;
        }
    }

    if (!empty($caches)) {
        $cachedata = cachedata($caches);
        foreach ($cachedata as $cname => $data) {
            if ($cname == 'setting') {
                $_G['setting'] = $data;
            } elseif (strexists($cname, 'usergroup_' . $_G['groupid'])) {
                $_G['cache'][$cname] = $_G['perm'] = $_G['group'] = $data;
            } elseif (!$_G['uid'] && strexists($cname, $_G['setting']['newusergroupid'])) {
                $_G['perm'] = $data;
            } elseif ($cname == 'style_default') {
                $_G['cache'][$cname] = $_G['style'] = $data;
            } elseif ($cname == 'grouplevels') {
                $_G['grouplevels'] = $data;
            } else {
                $_G['cache'][$cname] = $data;
            }
        }
    }
    return true;
}

/**
 * 更新缓存
 * @param $cachename - 缓存名称
 * @param $data - 缓存数据
 */
function save_syscache($cachename, $data) {
    static $isfilecache, $allowmem;
    if ($isfilecache === null) {
        $isfilecache = getglobal('config/cache/type') == 'file';
        $allowmem = memory('check');
    }

    if (is_array($data)) {
        $ctype = 1;
        $data = addslashes(serialize($data));
    } else {
        $ctype = 0;
    }

    DB::query("REPLACE INTO " . DB::table('common_syscache') . " (cname, ctype, dateline, data) VALUES ('$cachename', '$ctype', '" . TIMESTAMP . "', '$data')");

    $allowmem && memory('rm', $cachename);
    $isfilecache && @unlink(DISCUZ_ROOT . './data/cache/cache_' . $cachename . '.php');
}

function cachedata($cachenames) {
    global $_G;
    static $isfilecache, $allowmem;

    if (!isset($isfilecache)) {
        $isfilecache = getglobal('config/cache/type') == 'file';
        $allowmem = memory('check');
    }

    $data = array();
    $cachenames = is_array($cachenames) ? $cachenames : array($cachenames);
    if ($allowmem) {
        $newarray = array();
        foreach ($cachenames as $name) {
            $data[$name] = memory('get', $name);
            if ($data[$name] === null) {
                $data[$name] = null;
                $newarray[] = $name;
            }
        }
        if (empty($newarray)) {
            return $data;
        } else {
            $cachenames = $newarray;
        }
    }

    foreach ($cachenames as $name) {
        if ($data[$name] === null) {
            $data[$name] = null;
            $allowmem && (memory('set', $name, array()));
        }
    }
    return $data;
}

/**
 * 格式化时间
 * @param $timestamp - 时间戳
 * @param $format - dt=日期时间 d=日期 t=时间 u=个性化 其他=自定义
 * @param $timeoffset - 时区
 * @return string
 */
function dgmdate($timestamp, $format = 'dt', $timeoffset = '9999', $uformat = '') {
    global $_G;
    $dateconvert = 1;
    $format == 'u' && !$dateconvert && $format = 'dt';
    static $dformat, $tformat, $dtformat, $offset, $lang;
    if ($dformat === null) {
        $dformat = 'Y-n-j'; //getglobal('setting/dateformat');
        $tformat = 'H:i'; //getglobal('setting/timeformat');
        $dtformat = $dformat . ' ' . $tformat;
        $offset = '8'; //getglobal('member/timeoffset');
        $lang = lang('core', 'date');
    }
    $timeoffset = $timeoffset == 9999 ? $offset : $timeoffset;
    $timestamp += $timeoffset * 3600;
    $format = empty($format) || $format == 'dt' ? $dtformat : ($format == 'd' ? $dformat : ($format == 't' ? $tformat : $format));
    if ($format == 'u') {
        $todaytimestamp = TIMESTAMP - (TIMESTAMP + $timeoffset * 3600) % 86400 + $timeoffset * 3600;
        $s = gmdate(!$uformat ? $dtformat : $uformat, $timestamp);
        $time = TIMESTAMP + $timeoffset * 3600 - $timestamp;
        if ($timestamp >= $todaytimestamp) {
            if ($time > 3600) {
                return '<span title="' . $s . '">' . intval($time / 3600) . '&nbsp;' . $lang['hour'] . $lang['before'] . '</span>';
            } elseif ($time > 1800) {
                return '<span title="' . $s . '">' . $lang['half'] . $lang['hour'] . $lang['before'] . '</span>';
            } elseif ($time > 60) {
                return '<span title="' . $s . '">' . intval($time / 60) . '&nbsp;' . $lang['min'] . $lang['before'] . '</span>';
            } elseif ($time > 0) {
                return '<span title="' . $s . '">' . $time . '&nbsp;' . $lang['sec'] . $lang['before'] . '</span>';
            } elseif ($time == 0) {
                return '<span title="' . $s . '">' . $lang['now'] . '</span>';
            } else {
                return $s;
            }
        } elseif (($days = intval(($todaytimestamp - $timestamp) / 86400)) >= 0 && $days < 7) {
            if ($days == 0) {
                return '<span title="' . $s . '">' . $lang['yday'] . '&nbsp;' . gmdate($tformat, $timestamp) . '</span>';
            } elseif ($days == 1) {
                return '<span title="' . $s . '">' . $lang['byday'] . '&nbsp;' . gmdate($tformat, $timestamp) . '</span>';
            } else {
                return '<span title="' . $s . '">' . ($days + 1) . '&nbsp;' . $lang['day'] . $lang['before'] . '</span>';
            }
        } else {
            return $s;
        }
    } else {
        return gmdate($format, $timestamp);
    }
}

function strgmdate($time) {
    $time = substr($time, 6, 10);
    if ($time == 0) {
        return '';
    }
    return dgmdate($time);
}

/**
 * 得到时间戳
 *
 */
function dmktime($date) {
    if (strpos($date, '-')) {
        $time = explode('-', $date);
        return mktime(0, 0, 0, $time[1], $time[2], $time[0]);
    }
    return 0;
}

//连接字符
function dimplode($array) {
    if (!empty($array)) {
        return "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
    } else {
        return 0;
    }
}

/**
 * 返回库文件的全路径
 *
 * @param string $libname 库文件分类及名称
 * @return string
 *
 * @example require DISCUZ_ROOT.'./source/function/function_cache.php'
 * @example 我们可以利用此函数简写为：require libfile('function/cache');
 *
 */
function libfile($libname, $folder = '') {
    $libpath = DISCUZ_ROOT . '/source/' . $folder;
    if (strstr($libname, '/')) {
        list($pre, $name) = explode('/', $libname);
        return realpath("{$libpath}/{$pre}/{$pre}_{$name}.php");
    } else {
        return realpath("{$libpath}/{$libname}.php");
    }
}

/**
 * 根据中文裁减字符串
 * @param $string - 字符串
 * @param $length - 长度
 * @param $doc - 缩略后缀
 * @return 返回带省略号被裁减好的字符串
 */
function cutstr($string, $length, $dot = ' ...') {
    if (strlen($string) <= $length) {
        return $string;
    }

    $pre = chr(1);
    $end = chr(1);
    //保护特殊字符串
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);

    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8') {

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }

    //还原特殊字符串
    $strcut = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    //修复出现特殊字符串截段的问题
    $pos = strrpos($s, chr(1));
    if ($pos !== false) {
        $strcut = substr($s, 0, $pos);
    }
    return $strcut . $dot;
}

//去掉slassh
function dstripslashes($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dstripslashes($val);
        }
    } else {
        $string = stripslashes($string);
    }
    return $string;
}

function sendmail($email, $subject, $mailmsg) {
    $mail = new SaeMail();
    $mailmsg = "<style>body, input, button, select, textarea { font: 12px/1.5 microsoft yahei, verdana, arial;color: #444;} a, a:visited, a:a:link{text-decoration: none;}</style><div style=\"width:660px;margin:0 auto;padding:20px;background-color:#f4f4f4;font-size:14px;color:#4d4d4d;line-height:1.5;margin-bottom:30px;border: 1px solid #D4D4D4;\"><a href=\"http://addon.sinaapp.com/\" _target=\"_blank\" style=\"font-size:22px; font-weight:bold;\">Saion 应用服务平台</a><br><br><br>{$mailmsg}<br><br><br><div align=\"center\" style=\"font-size: 12px;color: #ccc;\">-------- 如该邮件在垃圾邮箱中，请添加为信任地址，以便接收来自 <a href=\"http://addon.sinaapp.com/\" _target=\"_blank\">Saion</a> 服务平台的通知 --------</div><br><br><div style=\"float:right;font-size: 12px;color: #ccc;\">(c) 2012 <a href=\"http://addon.sinaapp.com/\" _target=\"_blank\">Saion</a><br>此为系统邮件，请勿回复</div><br><br></div>";
    $mail->setOpt(array('from' => 'help@bbsapp.cc', 'to' => $email, 'smtp_host' => 'smtp.exmail.qq.com', 'smtp_port' => '465', 'smtp_username' => 'help@bbsapp.cc', 'smtp_password' => 'a84707367', 'subject' => $subject, 'content' => $mailmsg, 'content_type' => 'HTML', 'charset' => 'utf-8'));
    $mail->send();
}

/**
 * 调试信息
 */
function debuginfo() {
    global $_G;
    $db = & DB::object();
    $_G['debuginfo'] = array('time' => number_format((dmicrotime() - $_G['starttime']), 4), 'queries' => $db->querynum, 'memory' => ucwords($_G['memory']));
    return TRUE;
}

/**
 * 解析模板
 * @return 返回域名
 */
function template($file, $templateid = 0, $tpldir = '', $gettplfile = 0, $primaltpl = '') {
    global $_G;

    $file .=!empty($_G['inajax']) && ($file == 'common/header' || $file == 'common/footer') ? '_ajax' : '';
    $tpldir = $tpldir ? $tpldir : (defined('TPLDIR') ? TPLDIR : '');
    $templateid = $templateid ? $templateid : (defined('TEMPLATEID') ? TEMPLATEID : '');
    $tplfile = ($tpldir ? $tpldir . '/' : './template/') . $file . '.php';
    if (!is_file(DISCUZ_ROOT . $tplfile)) {
        $tplfile = ($tpldir ? $tpldir . '/' : './template/') . $file . '.htm';
    }   
    if (!is_file(DISCUZ_ROOT . $tplfile)) {
        $tips = lang('error', 'error_not_template', array('file' => $tplfile));
        system_error($tips);
    }
    $filebak = $file;

    return DISCUZ_ROOT . $tplfile;
}

function memory($cmd, $key = '', $value = '', $ttl = 0) {
    $discuz = & discuz_core::instance();
    if ($cmd == 'check') {
        return $discuz->mem->enable ? $discuz->mem->type : '';
    } elseif ($discuz->mem->enable && in_array($cmd, array('set', 'get', 'rm'))) {
        switch ($cmd) {
            case 'set': return $discuz->mem->set($key, $value, $ttl);
                break;
            case 'get': return $discuz->mem->get($key);
                break;
            case 'rm': return $discuz->mem->rm($key);
                break;
        }
    }
    return null;
}

/**
 * 系统输出
 * @return 返回内容
 */
function output() {
    global $_G;

    if (defined('DISCUZ_OUTPUTED')) {
        return;
    } else {
        define('DISCUZ_OUTPUTED', 1);
    }
    $content = ob_get_contents();
    $content = output_replace($content);


    ob_end_clean();
    $_G['gzipcompress'] ? ob_start('ob_gzhandler') : ob_start();

    echo $content;

    if (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG && @include(libfile('function/debug'))) {
        function_exists('debugmessage') && debugmessage();
    }
}

function output_replace($content) {
    global $_G;

    if (!empty($_G['setting']['output']['str']['search'])) {
        if (empty($_G['setting']['domain']['app']['default'])) {
            $_G['setting']['output']['str']['replace'] = str_replace('{CURHOST}', $_G['siteurl'], $_G['setting']['output']['str']['replace']);
        }
        $content = str_replace($_G['setting']['output']['str']['search'], $_G['setting']['output']['str']['replace'], $content);
    }
    if (!empty($_G['setting']['output']['preg']['search'])) {
        if (empty($_G['setting']['domain']['app']['default'])) {
            $_G['setting']['output']['preg']['search'] = str_replace('\{CURHOST\}', preg_quote($_G['siteurl'], '/'), $_G['setting']['output']['preg']['search']);
            $_G['setting']['output']['preg']['replace'] = str_replace('{CURHOST}', $_G['siteurl'], $_G['setting']['output']['preg']['replace']);
        }

        $content = preg_replace($_G['setting']['output']['preg']['search'], $_G['setting']['output']['preg']['replace'], $content);
    }

    return $content;
}

function output_ajax() {
    $s = ob_get_contents();
    ob_end_clean();
    $s = preg_replace("/([\\x01-\\x08\\x0b-\\x0c\\x0e-\\x1f])+/", ' ', $s);
    $s = str_replace(array(chr(0), ']]>'), array(' ', ']]&gt;'), $s);
    if (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG && @include(libfile('function/debug'))) {
        function_exists('debugmessage') && $s .= debugmessage(1);
    }
    return $s;
}

function runhooks() {
    global $_G;
    if (defined('CURMODULE')) {
        hookscript(CURMODULE, $_G['basescript']);
        if (($do = !empty($_G['gp_do']) ? $_G['gp_do'] : (!empty($_GET['do']) ? $_GET['do'] : ''))) {
            hookscript(CURMODULE, $_G['basescript'] . '_' . $do);
        }
    }
}

function hookscript($script, $hscript, $type = 'funcs', $param = array(), $func = '') {
    global $_G;
    static $pluginclasses;
    if (!isset($_G['setting']['hookscript'][$hscript][$script][$type])) {
        return;
    }
    if (!isset($_G['cache']['plugin'])) {
        loadcache('plugin');
    }
    foreach ((array) $_G['setting']['hookscript'][$hscript][$script]['module'] as $identifier => $include) {
        $hooksadminid[$identifier] = !$_G['setting']['hookscript'][$hscript][$script]['adminid'][$identifier] || ($_G['setting']['hookscript'][$hscript][$script]['adminid'][$identifier] && $_G['adminid'] > 0 && $_G['setting']['hookscript'][$hscript][$script]['adminid'][$identifier] >= $_G['adminid']);
        if ($hooksadminid[$identifier]) {
            @include_once DISCUZ_ROOT . './source/plugin/' . $include . '.class.php';
        }
    }
    if (@is_array($_G['setting']['hookscript'][$hscript][$script][$type])) {
        $funcs = !$func ? $_G['setting']['hookscript'][$hscript][$script][$type] : array($func => $_G['setting']['hookscript'][$hscript][$script][$type][$func]);
        foreach ($funcs as $hookkey => $hookfuncs) {
            foreach ($hookfuncs as $hookfunc) {
                if ($hooksadminid[$hookfunc[0]]) {
                    $classkey = 'plugin_' . ($hookfunc[0] . ($hscript != 'global' ? '_' . $hscript : ''));
                    if (!class_exists($classkey)) {
                        continue;
                    }
                    if (!isset($pluginclasses[$classkey])) {
                        $pluginclasses[$classkey] = new $classkey;
                    }
                    if (!method_exists($pluginclasses[$classkey], $hookfunc[1])) {
                        continue;
                    }
                    $return = $pluginclasses[$classkey]->$hookfunc[1]($param);
                    if (is_array($return)) {
                        foreach ($return as $k => $v) {
                            $_G['setting']['pluginhooks'][$hookkey][$k] .= $v;
                        }
                    } else {
                        $_G['setting']['pluginhooks'][$hookkey] .= $return;
                    }
                }
            }
        }
    }
}

function hookscriptoutput($tplfile) {
    global $_G;
    hookscript('global', 'global');
    if (defined('CURMODULE')) {
        $param = array('template' => $tplfile, 'message' => $_G['hookscriptmessage'], 'values' => $_G['hookscriptvalues']);
        hookscript(CURMODULE, $_G['basescript'], 'outputfuncs', $param);
        if (($do = !empty($_G['gp_do']) ? $_G['gp_do'] : (!empty($_GET['do']) ? $_GET['do'] : ''))) {
            hookscript(CURMODULE, $_G['basescript'] . '_' . $do, 'outputfuncs', $param);
        }
    }
}

function pluginmodule($pluginid, $type) {
    global $_G;
    if (!isset($_G['cache']['plugin'])) {
        loadcache('plugin');
    }
    list($identifier, $module) = explode(':', $pluginid);
    if (!is_array($_G['setting']['plugins'][$type]) || !array_key_exists($pluginid, $_G['setting']['plugins'][$type])) {
        showmessage('undefined_action');
    }
    if (!empty($_G['setting']['plugins'][$type][$pluginid]['url'])) {
        dheader('location: ' . $_G['setting']['plugins'][$type][$pluginid]['url']);
    }
    $directory = $_G['setting']['plugins'][$type][$pluginid]['directory'];
    if (empty($identifier) || !preg_match("/^[a-z]+[a-z0-9_]*\/$/i", $directory) || !preg_match("/^[a-z0-9_\-]+$/i", $module)) {
        showmessage('undefined_action');
    }
    if (@!file_exists(DISCUZ_ROOT . ($modfile = './source/plugin/' . $directory . $module . '.inc.php'))) {
        showmessage('plugin_module_nonexistence', '', array('mod' => $modfile));
    }
    return DISCUZ_ROOT . $modfile;
}

/**
 * 分页
 * @param $num - 总数
 * @param $perpage - 每页数
 * @param $curpage - 当前页
 * @param $mpurl - 跳转的路径
 * @param $maxpages - 允许显示的最大页数
 * @param $page - 最多显示多少页码
 * @param $autogoto - 最后一页，自动跳转
 * @param $simple - 是否简洁模式（简洁模式不显示上一页、下一页和页码跳转）
 * @return 返回分页代码
 */
function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = FALSE, $simple = FALSE) {
    global $_G;
    //debug 加入 ajaxtarget 属性
    $ajaxtarget = !empty($_G['gp_ajaxtarget']) ? " ajaxtarget=\"" . htmlspecialchars($_G['gp_ajaxtarget']) . "\" " : '';

    //note 处理#描点
    $a_name = '';
    if (strpos($mpurl, '#') !== FALSE) {
        $a_strs = explode('#', $mpurl);
        $mpurl = $a_strs[0];
        $a_name = '#' . $a_strs[1];
    }

    if (defined('IN_ADMINCP')) {
        $shownum = $showkbd = TRUE;
        $lang['prev'] = '&lsaquo;&lsaquo;';
        $lang['next'] = '&rsaquo;&rsaquo;';
    } else {
        $shownum = $showkbd = FALSE;
        $lang['prev'] = '&nbsp;&nbsp;';
        $lang['next'] = lang('core', 'nextpage');
    }

    $multipage = '';
    $mpurl .= strpos($mpurl, '?') !== FALSE ? '&amp;' : '?';

    $realpages = 1;
    $_G['page_next'] = 0;
    if ($num > $perpage) {

        $offset = floor($page * 0.5);

        $realpages = @ceil($num / $perpage);
        $pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

        if ($page > $pages) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $curpage - $offset;
            $to = $from + $page - 1;
            if ($from < 1) {
                $to = $curpage + 1 - $from;
                $from = 1;
                if ($to - $from < $page) {
                    $to = $page;
                }
            } elseif ($to > $pages) {
                $from = $pages - $page + 1;
                $to = $pages;
            }
        }
        $_G['page_next'] = $to;

        $multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="' . $mpurl . 'page=1' . $a_name . '" class="first"' . $ajaxtarget . '>1 ...</a>' : '') .
                ($curpage > 1 && !$simple ? '<a href="' . $mpurl . 'page=' . ($curpage - 1) . $a_name . '" class="prev"' . $ajaxtarget . '>' . '上一页' . '</a>' : '');
        for ($i = $from; $i <= $to; $i++) {
            $multipage .= $i == $curpage ? '<strong>' . $i . '</strong>' :
                    '<a href="' . $mpurl . 'page=' . $i . ($ajaxtarget && $i == $pages && $autogoto ? '#' : $a_name) . '"' . $ajaxtarget . '>' . $i . '</a>';
        }

        $multipage .= ($to < $pages ? '<a href="' . $mpurl . 'page=' . $pages . $a_name . '" class="last"' . $ajaxtarget . '>... ' . $realpages . '</a>' : '') .
                ($curpage < $pages && !$simple ? '<a href="' . $mpurl . 'page=' . ($curpage + 1) . $a_name . '" class="nxt"' . $ajaxtarget . '>' . $lang['next'] . '</a>' : '') .
                ($showkbd && !$simple && $pages > $page && !$ajaxtarget ? '<kbd><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\'' . $mpurl . 'page=\'+this.value; doane(event);}" /></kbd>' : '');

        $multipage = $multipage ? '<div class="pg">' . ($shownum && !$simple ? '<em>&nbsp;' . $num . '&nbsp;</em>' : '') . $multipage . '</div>' : '';
    }
    $maxpage = $realpages;
    return $multipage;
}

/**
 * 只有上一页下一页的分页（无需知道数据总数）
 * @param $num - 本次所取数据条数
 * @param $perpage - 每页数
 * @param $curpage - 当前页
 * @param $mpurl - 跳转的路径
 * @return 返回分页代码
 */
function simplepage($num, $perpage, $curpage, $mpurl) {
    $return = '';
    $lang['next'] = lang('core', 'nextpage');
    $lang['prev'] = lang('core', 'prevpage');
    $next = $num == $perpage ? '<a href="' . $mpurl . '&amp;page=' . ($curpage + 1) . '" class="nxt">' . $lang['next'] . '</a>' : '';
    $prev = $curpage > 1 ? '<span class="pgb"><a href="' . $mpurl . '&amp;page=' . ($curpage - 1) . '">' . $lang['prev'] . '</a></span>' : '';
    if ($next || $prev) {
        $return = '<div class="pg">' . $prev . $next . '</div>';
    }
    return $return;
}

/**
 * 编码转换
 * @param <string> $str 要转码的字符
 * @param <string> $in_charset 输入字符集
 * @param <string> $out_charset 输出字符集(默认当前)
 * @param <boolean> $ForceTable 强制使用码表(默认不强制)
 *
 */
function diconv($str, $in_charset, $out_charset = CHARSET, $ForceTable = FALSE) {
    global $_G;

    $in_charset = strtoupper($in_charset);
    $out_charset = strtoupper($out_charset);
    if ($in_charset != $out_charset) {
        require_once libfile('class/chinese');
        $chinese = new Chinese($in_charset, $out_charset, $ForceTable);
        $strnew = $chinese->Convert($str);
        if (!$ForceTable && !$strnew && $str) {
            $chinese = new Chinese($in_charset, $out_charset, 1);
            $strnew = $chinese->Convert($str);
        }
        return $strnew;
    } else {
        return $str;
    }
}

/**
 * 重建数组
 * @param <string> $array 需要反转的数组
 * @return array 原数组与的反转后的数组
 */
function renum($array) {
    $newnums = $nums = array();
    foreach ($array as $id => $num) {
        $newnums[$num][] = $id;
        $nums[$num] = $num;
    }
    return array($nums, $newnums);
}

/**
 * 字节格式化单位
 * @param $filesize - 大小(字节)
 * @return 返回格式化后的文本
 */
function sizecount($size) {
    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' GB';
    } elseif ($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' MB';
    } elseif ($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' KB';
    } else {
        $size = $size . ' Bytes';
    }
    return $size;
}

function ajaxshow($message) {
    ob_end_clean();
    ob_start();
    @header("Expires: -1");
    @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
    @header("Pragma: no-cache");
    @header("Content-type: text/xml; charset=GBK");
    echo '<?xml version="1.0" encoding="GBK"?>' . "\r\n";
    echo '<root><![CDATA[' . $message . ']]></root>';
}

function showmessage($message, $url_forward = '', $values = array(), $extraparam = array(), $custom = 0) {
    require_once libfile('function/message');
    return dshowmessage($message, $url_forward, $values, $extraparam, $custom);
}

//获取超时时间
function getexpiration() {
    global $_G;
    $date = getdate($_G['timestamp']);
    return mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']) + 86400;
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val) - 1});
    switch ($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function dis_array($arr = null) {
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            if ($v && !is_array($v)) {
                return false;
            }
            $t = dis_array($v);
            if (!$t) {
                return false;
            }
        }
        return true;
    } elseif (!$arr) {
        return true;
    } else {
        return false;
    }
}

function passcode($password) {
    return md5('6bbf69e5dffb35bb-' . $password . '-Saion');
}

/**
 * 检查是否正确提交了表单
 * @param $var 需要检查的变量
 * @param $allowget 是否允许GET方式
 * @param $seccodecheck 验证码检测是否开启
 * @return 返回是否正确提交了表单
 */
function submitcheck($var, $allowget = 0, $seccodecheck = 0, $secqaacheck = 0) {
    global $_G;
    if (!getgpc($var)) {
        return FALSE;
    }
    if ($allowget || ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_G['input']['formhash']) && $_G['input']['formhash'] == formhash() && empty($_SERVER['HTTP_X_FLASH_VERSION']) && (empty($_SERVER['HTTP_REFERER']) ||
            preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
        return TRUE;
    }
    if ($_G['input']['ajax']) {
        $json = array(
            'status' => 'error',
            'message' => '抱歉，您的请求来路不正确或表单验证串不符，无法提交',
        );
        echo jquery_json($json);
        exit();
    }
    showmessage('抱歉，您的请求来路不正确或表单验证串不符，无法提交');
}
