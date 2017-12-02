<?php

/*
 *  $ Discuz! Frame v0.1  (C) 2011-2012
 *  $ class_core.php
 *  + 2011-10-06  创建文件
 *  + 2011-10-07  基本架构
 */


define('IN_DISCUZ', TRUE);

/**
 * class discuz_core
 *
 * Description for class discuz_core
 *
 */
class core {
    private static $_tables;
    private static $_imports;

    // 数据库存储引擎
    var $db = null;
    // 内存缓冲object
    var $mem = null;
    // 会话 object
    var $session = null;
    // 程序配置
    var $config = array();
    // $_G 数组的映射
    var $var = array();
    // 加载缓存的数组
    var $cachelist = array();
    // 是否初始化
    var $init_setting = true;
    var $init_user = true;
    var $init_session = true;
    var $init_memory = true;
    // 是否已经初始化
    var $initated = false;
    var $superglobal = array(
        'GLOBALS' => 1,
        '_GET' => 1,
        '_POST' => 1,
        '_REQUEST' => 1,
        '_COOKIE' => 1,
        '_SERVER' => 1,
        '_ENV' => 1,
        '_FILES' => 1,
    );

    static function &instance() {
        static $object;
        if (empty($object)) {
            $object = new core();
        }
        return $object;
    }

    public static function t($name) {
        return self::_make_obj($name, 'table');
    }

    public static function m($name) {
        $cname = 'module_'.$name;
        if (!isset(self::$_tables[$cname])) {
            if (!class_exists($cname,false)) {
                self::import("module/$name");
            }
            self::$_tables[$cname] = new $cname();
        }
        return self::$_tables[$cname];
    }

    protected static function _make_obj($name, $type, $extendable = false, $p = array()) 
    {/*{{{*/
        $pluginid = null;
        if($name[0] === '#') {
            list(, $pluginid, $name) = explode('#', $name);
        }
        $cname = $type.'_'.$name;
        if(!isset(self::$_tables[$cname])) {
            if(!class_exists($cname, false)) {
                self::import(($pluginid ? 'plugin/'.$pluginid : 'class').'/'.$type.'/'.$name);
            }
            if($extendable) {
                self::$_tables[$cname] = new discuz_container();
                switch (count($p)) {
                    case 0: self::$_tables[$cname]->obj = new $cname();break;
                    case 1: self::$_tables[$cname]->obj = new $cname($p[1]);break;
                    case 2: self::$_tables[$cname]->obj = new $cname($p[1], $p[2]);break;
                    case 3: self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3]);break;
                    case 4: self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4]);break;
                    case 5: self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4], $p[5]);break;
                    default: $ref = new ReflectionClass($cname);self::$_tables[$cname]->obj = $ref->newInstanceArgs($p);unset($ref);break;
                }
            } else {
                self::$_tables[$cname] = new $cname();
            }
        }
        return self::$_tables[$cname];
    }/*}}}*/

    public static function import($name, $folder = '', $force = true) 
    {/*{{{*/
        $key = $folder.$name;
        if(!isset(self::$_imports[$key])) {
            $path = DISCUZ_ROOT.'/source/'.$folder;
            if(strpos($name, '/') !== false) {
                $pre = basename(dirname($name));
                $filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
            } else {
                $filename = $name.'.php';
            }   

            if(is_file($path.'/'.$filename)) {
                include $path.'/'.$filename;
                self::$_imports[$key] = true;

                return true;
            } elseif(!$force) {
                return false;
            } else {
                throw new Exception('Oops! System file lost: '.$filename);
            }   
        }   
        return true;
    }/*}}}*/


    function core() {
        $this->_init_env();
        $this->_init_config();
        $this->_init_input();
        $this->_init_output();
    }

    function init() {
        if (!$this->initated) {
            $this->_init_db();
            $this->_init_user();
            $this->_init_misc();
        }
        $this->initated = true;
    }

    function _init_env() {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        if (phpversion() < '5.3.0') {
            set_magic_quotes_runtime(0);
        }
        define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -12));
        define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
        define('ICONV_ENABLE', function_exists('iconv'));
        define('MB_ENABLE', function_exists('mb_convert_encoding'));
        define('EXT_OBGZIP', function_exists('ob_gzhandler'));
        define('STYLEID', '1');
        define('TIMESTAMP', time());
        define('ROBOT_WEB_VERSION', '1.0');
        define('ROBOT_WEB_RELEASE', '20150401');
        define('ROBOT_CLIENT_VERSION', '1.0');
        define('ROBOT_CLIENT_RELEASE', '20150401');
        $this->timezone_set();

        if (!defined('DISCUZ_CORE_FUNCTION') && !@include(DISCUZ_ROOT . './source/function/function_core.php')) {
            exit('function_core.php is missing');
        }

        /**
         * 部分php环境内存设置过低，导致程序无法正常工作，此处判断当内存分配小于32M时，将内存加大至 32M
         * 经测试，系统如果php限制内存小于8M时程序将会运行异常
         */
        if (function_exists('ini_get')) {
            $memorylimit = @ini_get('memory_limit');
            if ($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
                ini_set('memory_limit', '128m');
            }
        }

        //模拟本地Sae服务
        //include DISCUZ_ROOT.'./source/SaeImit/SaeImit.php';
        //清理全局变量
        foreach ($GLOBALS as $key => $value) {
            if (!isset($this->superglobal[$key])) {
                $GLOBALS[$key] = null;
                unset($GLOBALS[$key]);
            }
        }

        // 配置全局变量
        global $_G;

        $_G = array(
            //公用全局定义
            'uid' => 0,
            'username' => '',
            'adminid' => 0,
            'groupid' => 1,
            'sid' => '',
            'formhash' => '',
            'timestamp' => TIMESTAMP,
            'starttime' => dmicrotime(),
            'clientip' => $this->_get_client_ip(),
            'referer' => '',
            'charset' => '',
            'gzip_open' => '1',
            'authkey' => '',
            'timenow' => array(),
            'PHP_SELF' => '',
            'siteurl' => '',
            'siteroot' => '',
            'sitename' => '',
            //公用全局数组定义
            'config' => array(),
            'setting' => array(),
            'member' => array(),
            'cookie' => array(),
            'tpc' => array(),
            'cache' => array(),
            'session' => array()
        );

        $_G['PHP_SELF'] = htmlspecialchars($_SERVER['SCRIPT_NAME'] ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF']);
        $_G['basescript'] = CURSCRIPT;
        $_G['basefilename'] = basename($_G['PHP_SELF']);
        $_G['siteurl'] = htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . preg_replace("/\/+(api)?\/*$/i", '', substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'))) . '/');
        $_G['siteroot'] = substr($_G['PHP_SELF'], 0, -strlen($_G['basefilename']));
        if (defined('SUB_DIR')) {
            $_G['siteurl'] = str_replace(SUB_DIR, '/', $_G['siteurl']);
            $_G['siteroot'] = str_replace(SUB_DIR, '/', $_G['siteroot']);
        }

        $this->var = &$_G;
    }

    function _init_input() {
        //note 禁止对全局变量注入
        if (isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
            system_error('request_tainting');
        }

        // slashes 处理
        if (MAGIC_QUOTES_GPC) {
            $_GET = dstripslashes($_GET);
            $_POST = dstripslashes($_POST);
            $_COOKIE = dstripslashes($_COOKIE);
        }
        $prelength = strlen($this->config['cookie']['cookiepre']);
        foreach ($_COOKIE as $key => $val) {
            if (substr($key, 0, $prelength) == $this->config['cookie']['cookiepre']) {
                $this->var['cookie'][substr($key, $prelength)] = $val;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $_GET = array_merge($_GET, $_POST);
        }
        foreach ($_GET as $k => $v) {
            $this->var['input'][$k] = daddslashes($v);
        }

        $this->var['sid'] = $this->var['cookie']['sid'] = isset($this->var['cookie']['sid']) ? htmlspecialchars($this->var['cookie']['sid']) : '';
        $this->var['page'] = empty($this->var['gp_page']) ? 1 : max(1, intval($this->var['gp_page']));

        if (empty($this->var['cookie']['saltkey'])) {
            $this->var['cookie']['saltkey'] = random(8);
            dsetcookie('saltkey', $this->var['cookie']['saltkey']);
        }
        $this->var['authkey'] = md5($this->var['config']['security']['authkey'] . $this->var['cookie']['saltkey']);
    }

    function _init_config() {
        $_config = array();
        @include DISCUZ_ROOT . './config/config.php';
        //CONFIG::DEBUG
        if (empty($_config['debug']) || !file_exists(libfile('function/debug'))) {
            define('DISCUZ_DEBUG', false);
        } elseif ($_config['debug'] === 1 || $_config['debug'] === 2 || !empty($_REQUEST['debug']) && $_REQUEST['debug'] === $_config['debug']) {
            define('DISCUZ_DEBUG', true);
            if ($_config['debug'] == 2) {
                error_reporting(E_ALL);
            }
        } else {
            define('DISCUZ_DEBUG', false);
        }

        $this->config = &$_config;
        $this->var['config'] = & $_config;
        $this->var['sitename'] = $_config['sitename'];
    }

    function _init_misc() {
        $this->var['timenow'] = array(
            'time' => dgmdate(TIMESTAMP),
                //'offset' => $timeoffset >= 0 ? ($timeoffset == 0 ? '' : '+'.$timeoffset) : $timeoffset
        );
    }

    function _init_user() {
        if ($this->init_user) {
            if ($auth = getglobal('auth', 'cookie')) {
                $auth = daddslashes(explode("\t", authcode($auth, 'DECODE')));
            }
            list($discuz_pw, $discuz_uid, $discuz_uin) = empty($auth) || count($auth) < 3 ? array('', '') : $auth;

            if ($discuz_uid) {
                $user = getuserbyuid($discuz_uid);
            }

            if ($user['uin'] != $discuz_uin) {
                dsetcookie('auth', '', '-1');
                showmessage('抱歉，您需要重新登录验证自己的帐号');
            } elseif ($user['status'] == '1' && CURMODULE !== 'active') {
                showmessage('抱歉，您需要验证激活自己的 QQ 身份后才能进行本操作');
            }

            if (!empty($user) && $user['password'] == $discuz_pw) {
                $this->var['member'] = $user;
            } else {
                $user = array();
                $this->_init_guest();
            }
        } else {
            $this->_init_guest();
        }

        if (empty($this->var['cookie']['lastvisit'])) {
            $this->var['member']['lastvisit'] = TIMESTAMP - 3600;
            dsetcookie('lastvisit', TIMESTAMP - 3600, 86400 * 30);
        } else {
            $this->var['member']['lastvisit'] = $this->var['cookie']['lastvisit'];
        }
        setglobal('uid', getglobal('uid', 'member'));
        setglobal('formhash', formhash());
        setglobal('username', addslashes(getglobal('username', 'member')));
        setglobal('adminid', getglobal('adminid', 'member'));
        setglobal('groupid', getglobal('groupid', 'member'));
    }

    function _init_guest() {
        setglobal('member', array('uid' => 0, 'username' => '', 'adminid' => 0, 'groupid' => 9, 'credits' => 0, 'timeoffset' => 9999));
    }

    function _init_db() {
        $this->db = & DB::object();
        $this->db->set_config($this->config['db']);
        $this->db->connect();
    }

    function _get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }

    function _init_output() {

        if ($this->config['security']['urlxssdefend'] && $_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_SERVER['REQUEST_URI'])) {
            $this->_xss_check();
        }

        if ($this->config['security']['attackevasive'] && (!defined('CURSCRIPT') || !in_array($this->var['mod'], array('seccode', 'secqaa', 'swfupload')))) {
            require_once libfile('misc/security', 'include');
        }

        ob_start();

        setglobal('charset', $this->config['output']['charset']);
        define('CHARSET', $this->config['output']['charset']);
        @header('Content-Type: text/html; charset=utf-8');
    }

    function _init_style() {
        $styleid = !empty($this->var['cookie']['styleid']) ? $this->var['cookie']['styleid'] : 0;
        if (intval(!empty($this->var['forum']['styleid']))) {
            $this->var['cache']['style_default']['styleid'] = $styleid = $this->var['forum']['styleid'];
        } elseif (intval(!empty($this->var['category']['styleid']))) {
            $this->var['cache']['style_default']['styleid'] = $styleid = $this->var['category']['styleid'];
        }

        $styleid = intval($styleid);

        if ($styleid && $styleid != $this->var['setting']['styleid']) {
            loadcache('style_' . $styleid);
            if ($this->var['cache']['style_' . $styleid]) {
                $this->var['style'] = $this->var['cache']['style_' . $styleid];
            }
        }

        define('IMGDIR', $this->var['style']['imgdir']);
        define('STYLEID', $this->var['style']['styleid']);
        define('VERHASH', $this->var['style']['verhash']);
        define('TPLDIR', $this->var['style']['tpldir']);
        define('TEMPLATEID', $this->var['style']['templateid']);
    }

    function timezone_set() {
        if (function_exists('date_default_timezone_set')) {
            @date_default_timezone_set('Etc/GMT+8');
        }
    }

}

class mysql {

    var $tablepre;
    var $version = '';
    var $querynum = 0;
    var $slaveid = 0;
    var $curlink;
    var $link = array();
    var $config = array();
    var $sqldebug = array();
    var $map = array();

    function db_mysql($config = array()) {
        if (!empty($config)) {
            $this->set_config($config);
        }
    }

    function set_config($config) {
        $this->config = &$config;
        $this->tablepre = $config['1']['tablepre'];
        if (!empty($this->config['map'])) {
            $this->map = $this->config['map'];
        }
    }

    function connect($serverid = 1) {

        if (empty($this->config) || empty($this->config[$serverid])) {
            $this->halt('config_db_not_found');
        }

        $this->link[$serverid] = $this->_dbconnect(
                $this->config[$serverid]['dbhost'], $this->config[$serverid]['dbuser'], $this->config[$serverid]['dbpw'], $this->config[$serverid]['dbcharset'], $this->config[$serverid]['dbname'], $this->config[$serverid]['pconnect']
        );
        $this->curlink = $this->link[$serverid];
    }

    function _dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect) {
        $link = null;
        $func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
        if (!$link = @$func($dbhost, $dbuser, $dbpw, 1)) {
            $this->halt('notconnect');
        } else {
            $this->curlink = $link;
            if ($this->version() > '4.1') {
                $dbcharset = $dbcharset ? $dbcharset : $this->config[1]['dbcharset'];
                $serverset = $dbcharset ? 'character_set_connection=' . $dbcharset . ', character_set_results=' . $dbcharset . ', character_set_client=binary' : '';
                $serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',') . 'sql_mode=\'\'') : '';
                $serverset && mysql_query("SET $serverset", $link);
            }
            $dbname && @mysql_select_db($dbname, $link);
        }
        return $link;
    }

    function table_name($tablename) {
        if (!empty($this->map) && !empty($this->map[$tablename])) {
            $id = $this->map[$tablename];
            if (!$this->link[$id]) {
                $this->connect($id);
            }
            $this->curlink = $this->link[$id];
        } else {
            $this->curlink = $this->link[1];
        }
        return $this->tablepre . $tablename;
    }

    function select_db($dbname) {
        return mysql_select_db($dbname, $this->curlink);
    }


    function fetch_array($query, $result_type = MYSQL_ASSOC) {
        return mysql_fetch_array($query, $result_type);
    }

    function fetch_first($sql) {
        return $this->fetch_array($this->query($sql));
    }

    function result_first($sql) {
        return $this->result($this->query($sql), 0);
    }

    function query($sql, $type = '') {

        if (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) {
            $starttime = dmicrotime();
        }
        $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
                'mysql_unbuffered_query' : 'mysql_query';
        if (!($query = $func($sql, $this->curlink))) {
            if (in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
                $this->connect();
                return $this->query($sql, 'RETRY' . $type);
            }
            if ($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                $this->halt('query_error', $sql);
            }
        }

        if (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) {
            $this->sqldebug[] = array($sql, number_format((dmicrotime() - $starttime), 6), debug_backtrace());
        }

        $this->querynum++;
        return $query;
    }

    function affected_rows() {
        return mysql_affected_rows($this->curlink);
    }

    function error() {
        return (($this->curlink) ? mysql_error($this->curlink) : mysql_error());
    }

    function errno() {
        return intval(($this->curlink) ? mysql_errno($this->curlink) : mysql_errno());
    }

    function result($query, $row = 0) {
        $query = @mysql_result($query, $row);
        return $query;
    }

    function num_rows($query) {
        $query = mysql_num_rows($query);
        return $query;
    }

    function num_fields($query) {
        return mysql_num_fields($query);
    }

    function free_result($query) {
        return mysql_free_result($query);
    }

    function insert_id() {
        return ($id = mysql_insert_id($this->curlink)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
    }

    function fetch_row($query) {
        $query = mysql_fetch_row($query);
        return $query;
    }

    function fetch_fields($query) {
        return mysql_fetch_field($query);
    }

    function version() {
        if (empty($this->version)) {
            $this->version = mysql_get_server_info($this->curlink);
        }
        return $this->version;
    }

    function close() {
        return mysql_close($this->curlink);
    }

    function halt($message = '', $sql = '') {
        require_once libfile('class/error');
        discuz_error::db_error($message, $sql);
    }

}

/**
 * 对Discuz CORE 中 DB Object中的主要方法进行二次封装，方便程序调用
 *
 */
class DB {

    /**
     * 返回表名(pre_$table)
     *
     * @param 原始表名 $table
     * @return 增加pre之后的名字
     */
    function table($table) {
        return DB::_execute('table_name', $table);
    }

    /**
     * 删除一条或者多条记录
     *
     * @param string $table 原始表名
     * @param string $condition 条件语句，不需要写WHERE
     * @param int $limit 删除条目数
     * @param boolean $unbuffered 立即返回？
     */
    function delete($table, $condition, $limit = 0, $unbuffered = true) {
        if (empty($condition)) {
            $where = '1';
        } elseif (is_array($condition)) {
            $where = DB::implode_field_value($condition, ' AND ');
        } else {
            $where = $condition;
        }
        $sql = "DELETE FROM " . DB::table($table) . " WHERE $where " . ($limit ? "LIMIT $limit" : '');
        return DB::query($sql, ($unbuffered ? 'UNBUFFERED' : ''));
    }

    /**
     * 插入一条记录
     *
     * @param string $table 原始表名
     * @param array $data 数组field->vlaue 对
     * @param boolen $return_insert_id 返回 InsertID?
     * @param boolen $replace 是否是REPLACE模式
     * @param boolen $silent 屏蔽错误？
     * @return InsertID or Result
     */
    function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false) {

        $sql = DB::implode_field_value($data);

        $cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';

        $table = DB::table($table);
        $silent = $silent ? 'SILENT' : '';

        $return = DB::query("$cmd $table SET $sql", $silent);

        return $return_insert_id ? DB::insert_id() : $return;
    }

    /**
     * 更新一条或者多条数据记录
     *
     * @param string $table 原始表名
     * @param array $data 数据field-value
     * @param string $condition 条件语句，不需要写WHERE
     * @param boolean $unbuffered 迅速返回？
     * @param boolan $low_priority 延迟更新？
     * @return result
     */
    function update($table, $data, $condition, $unbuffered = false, $low_priority = false) {
        $sql = DB::implode_field_value($data);
        $cmd = "UPDATE " . ($low_priority ? 'LOW_PRIORITY' : '');
        $table = DB::table($table);
        $where = '';
        if (empty($condition)) {
            $where = '1';
        } elseif (is_array($condition)) {
            $where = DB::implode_field_value($condition, ' AND ');
        } else {
            $where = $condition;
        }
        $res = DB::query("$cmd $table SET $sql WHERE $where", $unbuffered ? 'UNBUFFERED' : '');
        return $res;
    }

    /**
     * 格式化field字段和value，并组成一个字符串
     *
     * @param array $array 格式为 key=>value 数组
     * @param 分割符 $glue
     * @return string
     */
    function implode_field_value($array, $glue = ',') {
        $sql = $comma = '';
        foreach ($array as $k => $v) {
            $sql .= $comma . "`$k`='$v'";
            $comma = $glue;
        }
        return $sql;
    }

    /**
     * 返回插入的ID
     *
     * @return int
     */
    function insert_id() {
        return DB::_execute('insert_id');
    }

    /**
     * 依据查询结果，返回一行数据
     *
     * @param resourceID $resourceid
     * @return array
     */
    function fetch($resourceid, $type = MYSQL_ASSOC) {
        return DB::_execute('fetch_array', $resourceid, $type);
    }

    /**
     * 依据SQL文，返回一条查询结果
     *
     * @param string $query 查询语句
     * @return array
     */
    function fetch_first($sql) {
        DB::checkquery($sql);
        return DB::_execute('fetch_first', $sql);
    }

    /**
     * 依据SQL文，返回全部查询结果
     **/
    function fetch_all($sql) {
        $query = DB::query($sql);
        $res = array();
        while ($row = DB::fetch($query)) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * 依据查询结果，返回结果数值
     *
     * @param resourceid $resourceid
     * @return string or int
     */
    function result($resourceid, $row = 0) {
        return DB::_execute('result', $resourceid, $row);
    }

    /**
     * 依据查询语句，返回结果数值
     *
     * @param string $query SQL查询语句
     * @return unknown
     */
    function result_first($sql) {
        DB::checkquery($sql);
        return DB::_execute('result_first', $sql);
    }

    /**
     * 执行查询
     *
     * @param string $sql
     * @param 类型定义 $type UNBUFFERED OR SILENT
     * @return Resource OR Result
     */
    function query($sql, $type = '') {
        DB::checkquery($sql);
        return DB::_execute('query', $sql, $type);
    }

    /**
     * 返回select的结果行数
     *
     * @param resource $resourceid
     * @return int
     */
    function num_rows($resourceid) {
        return DB::_execute('num_rows', $resourceid);
    }

    /**
     * 返回sql语句所影响的记录行数
     *
     * @return int
     */
    function affected_rows() {
        return DB::_execute('affected_rows');
    }

    function free_result($query) {
        return DB::_execute('free_result', $query);
    }

    function error() {
        return DB::_execute('error');
    }

    function errno() {
        return DB::_execute('errno');
    }

    function _execute($cmd, $arg1 = '', $arg2 = '') {
        static $db;
        if (empty($db))
            $db = & DB::object();
        $res = $db->$cmd($arg1, $arg2);
        return $res;
    }

    /**
     * 返回 DB object 指针
     *
     * @return pointer of db object from discuz core
     */
    function &object() {
        global $_G;
        if ($_G['config']['mysqltype'] > 0) {
            $classname = 'mysql';
        } else {
            $classname = 'mysql';
        }
        static $db;
        if (empty($db)) {
            $db = new $classname();
        }
        return $db;
    }

    function checkquery($sql) {
        static $status = null, $checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLACE', 'DELETE');
        if ($status === null)
            $status = getglobal('config/security/querysafe/status');
        if ($status) {
            $cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
            if (in_array($cmd, $checkcmd)) {
                $test = DB::_do_query_safe($sql);
                if ($test < 1)
                    DB::_execute('halt', 'security_error', $sql);
            }
        }
        return true;
    }

}


class C extends core {}


