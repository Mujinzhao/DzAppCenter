<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class discuz_error {

    public static function system_error($message, $show = true, $save = true, $halt = true) {
        if (!empty($message)) {
            $message = lang('error', $message);
        } else {
            $message = lang('error', 'error_unknow');
        }

        list($showtrace, $logtrace) = discuz_error::debug_backtrace();

        if ($save) {
            $messagesave = '<b>' . $message . '</b><br><b>PHP:</b>' . $logtrace;
            discuz_error::write_error_log($messagesave);
        }

        if ($show) {
            if (!defined('IN_MOBILE')) {
                discuz_error::show_error('System', $message, $showtrace, 0);
            } else {
                discuz_error::mobile_show_error('system', "<li>$message</li>", $showtrace, 0);
            }
        }

        if ($halt) {
            exit();
        } else {
            return $message;
        }
    }

    public static function template_error($message, $tplname) {
        $message = lang('error', $message);
        $tplname = str_replace(DISCUZ_ROOT, '', $tplname);
        $message = $message . ': ' . $tplname;
        discuz_error::system_error($message);
    }

    public static function debug_backtrace() {
        $skipfunc[] = 'discuz_error::debug_backtrace';
        $skipfunc[] = 'discuz_error::db_error';
        $skipfunc[] = 'discuz_error::template_error';
        $skipfunc[] = 'discuz_error::system_error';
        $skipfunc[] = 'db_mysql::halt';
        $skipfunc[] = 'db_mysql::query';
        $skipfunc[] = 'DB::_execute';

        $show = $log = '';
        $debug_backtrace = debug_backtrace();
        krsort($debug_backtrace);
        foreach ($debug_backtrace as $k => $error) {
            $file = str_replace(DISCUZ_ROOT, '', $error['file']);
            $func = isset($error['class']) ? $error['class'] : '';
            $func .= isset($error['type']) ? '::' : '';
            $func .= isset($error['function']) ? $error['function'] : '';
            if (in_array($func, $skipfunc)) {
                break;
            }
            $error[line] = sprintf('%04d', $error['line']);
            if ($file == $refile && in_array($error['type'], array('->', '::'))) {
                $show .= '<br />' . dirname($file) . '/' . $func;
            } elseif ($file !== $refile) {
                $show .= '<br />' . $file . ' (Line: ' . $error[line] . ')';
            }
            $log .=!empty($log) ? ' -> ' : '';
            $file . ':' . $error['line'];
            $log .= $file . ':' . $error['line'];
            $refile = $file;
        }
        return array($show, $log);
    }

    public static function db_error($message, $sql) {
        global $_G;

        list($showtrace, $logtrace) = discuz_error::debug_backtrace();

        $title = lang('error', 'db_' . $message);
        $title_msg = lang('error', 'db_error_message');
        $title_sql = lang('error', 'db_query_sql');
        $title_backtrace = lang('error', 'backtrace');
        $title_help = lang('error', 'db_help_link');

        $db = &DB::object();
        $dberrno = $db->errno();
        $dberror = str_replace($db->tablepre, '', $db->error());
        $sql = dhtmlspecialchars(str_replace($db->tablepre, '', $sql));

        $msg = '<li>[Type] ' . $title . '</li>';
        $msg .= $dberrno ? '<li>[' . $dberrno . '] ' . $dberror . '</li>' : '';
        $msg .= $sql ? '<li>[Query] ' . $sql . '</li>' : '';

        discuz_error::show_error('db', $msg, $showtrace, false);
        unset($msg, $phperror);

        $errormsg = '<b>' . $title . '</b>';
        $errormsg .= "[$dberrno]<br /><b>ERR:</b> $dberror<br />";
        if ($sql) {
            $errormsg .= '<b>SQL:</b> ' . $sql;
        }
        $errormsg .= "<br />";
        $errormsg .= '<b>PHP:</b> ' . $logtrace;

        discuz_error::write_error_log($errormsg);
        exit();
    }

    public static function exception_error($exception) {

        if ($exception instanceof DbException) {
            $type = 'db';
        } else {
            $type = 'system';
        }

        if ($type == 'db') {
            $errormsg = '(' . $exception->getCode() . ') ';
            $errormsg .= self::sql_clear($exception->getMessage());
            if ($exception->getSql()) {
                $errormsg .= '<div class="sql">';
                $errormsg .= self::sql_clear($exception->getSql());
                $errormsg .= '</div>';
            }
        } else {
            $errormsg = $exception->getMessage();
        }

        $trace = $exception->getTrace();
        krsort($trace);

        $trace[] = array('file' => $exception->getFile(), 'line' => $exception->getLine(), 'function' => 'break');
        $phpmsg = array();
        foreach ($trace as $error) {
            if (!empty($error['function'])) {
                $fun = '';
                if (!empty($error['class'])) {
                    $fun .= $error['class'] . $error['type'];
                }
                $fun .= $error['function'] . '(';
                if (!empty($error['args'])) {
                    $mark = '';
                    foreach ($error['args'] as $arg) {
                        $fun .= $mark;
                        if (is_array($arg)) {
                            $fun .= 'Array';
                        } elseif (is_bool($arg)) {
                            $fun .= $arg ? 'true' : 'false';
                        } elseif (is_int($arg)) {
                            $fun .= (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) ? $arg : '%d';
                        } elseif (is_float($arg)) {
                            $fun .= (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) ? $arg : '%f';
                        } else {
                            $fun .= (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) ? '\'' . dhtmlspecialchars(substr(self::clear($arg), 0, 10)) . (strlen($arg) > 10 ? ' ...' : '') . '\'' : '%s';
                        }
                        $mark = ', ';
                    }
                }

                $fun .= ')';
                $error['function'] = $fun;
            }
            $phpmsg[] = array(
                'file' => str_replace(array(DISCUZ_ROOT, '\\'), array('', '/'), $error['file']),
                'line' => $error['line'],
                'function' => $error['function'],
            );
        }

        self::show_error($type, $errormsg, $phpmsg);
        exit();
    }

    public static function show_error($type, $errormsg, $phpmsg = '', $typemsg = '') {
        global $_G;

        ob_end_clean();
        $gzip = getglobal('gzipcompress');
        ob_start($gzip ? 'ob_gzhandler' : null);
        $host = $_SERVER['HTTP_HOST'];
        echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>System Error</title>
    <meta http-equiv="Content-Type" content="text/html; charset={$_G['config']['output']['charset']}" />
    <meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
    <style type="text/css">
    <!--
        html, body { width: 100%; height: 100%; }
        html, body, h2, h3, p, div { margin: 0; padding: 0; }
        body { background-color: #2980B9; font-family: "Microsoft YaHei", FreeSans, Arimo, "Droid Sans","wenquanyi micro hei","Hiragino Sans GB", "Hiragino Sans GB W3", Arial, sans-serif; }
        .page-error{padding: 60px 10px; text-align: center;}
            .page-error h3 { font-size: 24px; font-weight: 300; margin-top: 20px; margin-bottom: 10px; line-height: 1.1; color: #fff;}
            .page-error h3 span{ color: #4eb0f8; }
        .container{ width: 600px; padding: 15px; margin-top: 40px; margin-right: auto; margin-left: auto; background-color: #fff; }
            .container h2{ font-size: 30px; font-weight: 300; color: #e05d6f;}
            .container h2 strong { font-weight: 600; }
            .container h4{ margin-top: 40px; margin-bottom: 0; font-size: 18px; font-weight: 500; line-height: 1.1; }
            .container p{ color: #95a2a9; font-size: 12px;  margin: 12px 0px; }
        pre{ background-color: #3498DB; margin: -15px; padding: 15px; color: #fff; text-align:left; }
        .foot{font-size: 12px; margin-top:15px; color:#4eb0f8;}
    -->
    </style>
</head>
<body>
    <div class="page-error">
        <h3><span>D!</span>Framework</h3>
        <div class="container">
            <h2>$type Error - <strong>1024</strong></h2>
            $errormsg
EOT;
        if (!empty($phpmsg)) {
            echo '<pre class="info" style="margin-top:40px;">';
            echo 'BackTrace:';
            if (is_array($phpmsg)) {
                foreach ($phpmsg as $k => $msg) {
                    $k++;
                    echo '';
                    echo '' . $k . '';
                    echo '' . $msg['file'] . '';
                    echo '' . $msg['line'] . '';
                    echo '' . $msg['function'] . '';
                    echo "\n";
                }
            } else {
                echo '' . $phpmsg . "\n";
            }
            echo '</pre>';
        }


        $helplink = '';
        if ($type == 'db') {
            $helplink = "http://faq.comsenz.com/?type=mysql&dberrno=" . rawurlencode(DB::errno()) . "&dberror=" . rawurlencode(str_replace(DB::object()->tablepre, '', DB::error()));
            $helplink = "<a href=\"$helplink\" target=\"_blank\"><span class=\"red\">Need Help?</span></a>";
        }

        $endmsg = lang('error', 'error_end_message');
        echo <<<EOT
    </div>
        <p class="foot">$endmsg</p>
    </div>
</div>
</body>
</html>
EOT;
        $exit && exit();
    }

    public static function mobile_show_error($type, $errormsg, $phpmsg) {
        global $_G;

        ob_end_clean();
        ob_start();

        $host = $_SERVER['HTTP_HOST'];
        $phpmsg = trim($phpmsg);
        $title = 'Mobile ' . ($type == 'db' ? 'Database' : 'System');
        echo <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<head>
	<title>$host - $title Error</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
	<style type="text/css">
	<!--
	body { background-color: white; color: black; }
	UL, LI { margin: 0; padding: 2px; list-style: none; }
	#message   { color: black; background-color: #FFFFCC; }
	#bodytitle { font: 11pt/13pt verdana, arial, sans-serif; height: 20px; vertical-align: top; }
	.bodytext  { font: 8pt/11pt verdana, arial, sans-serif; }
	.help  { font: 12px verdana, arial, sans-serif; color: red;}
	.red  {color: red;}
	a:link     { font: 8pt/11pt verdana, arial, sans-serif; color: red; }
	a:visited  { font: 8pt/11pt verdana, arial, sans-serif; color: #4e4e4e; }
	-->
	</style>
</head>
<body>
<table cellpadding="1" cellspacing="1" id="container">
<tr>
	<td id="bodytitle" width="100%">Discuz! $title Error </td>
</tr>
EOT;

        echo <<<EOT
<tr><td><hr size="1"/></td></tr>
<tr><td class="bodytext">Error messages: </td></tr>
<tr>
	<td class="bodytext" id="message">
		<ul> $errormsg</ul>
	</td>
</tr>
EOT;
        if (!empty($phpmsg) && $type == 'db') {
            echo <<<EOT
<tr><td class="bodytext">&nbsp;</td></tr>
<tr><td class="bodytext">Program messages: </td></tr>
<tr>
	<td class="bodytext">
		<ul> $phpmsg </ul>
	</td>
</tr>
EOT;
        }
        $endmsg = lang('error', 'mobile_error_end_message', array('host' => $host));
        echo <<<EOT
<tr>
	<td class="help"><br />$endmsg</td>
</tr>
</table>
</body>
</html>
EOT;
        $exit && exit();
    }

    public static function clear($message) {
        return str_replace(array("\t", "\r", "\n"), " ", $message);
    }

    public static function sql_clear($message) {
        $message = self::clear($message);
        $message = str_replace(DB::object()->tablepre, '', $message);
        $message = dhtmlspecialchars($message);
        return $message;
    }

    public static function write_error_log($message) {

        $message = discuz_error::clear($message);
        $time = time();
        $file = DISCUZ_ROOT . './data/log/' . date("Ym") . '_errorlog.php';
        $hash = md5($message);

        $uid = getglobal('uid');
        $ip = getglobal('clientip');

        $user = '<b>User:</b> uid=' . intval($uid) . '; IP=' . $ip . '; RIP:' . $_SERVER['REMOTE_ADDR'];
        $uri = 'Request: ' . dhtmlspecialchars(discuz_error::clear($_SERVER['REQUEST_URI']));
        $message = "<?PHP exit;?>\t{$time}\t$message\t$hash\t$user $uri\n";
        if ($fp = @fopen($file, 'rb')) {
            $lastlen = 50000;
            $maxtime = 60 * 10;
            $offset = filesize($file) - $lastlen;
            if ($offset > 0) {
                fseek($fp, $offset);
            }
            if ($data = fread($fp, $lastlen)) {
                $array = explode("\n", $data);
                if (is_array($array))
                    foreach ($array as $key => $val) {
                        $row = explode("\t", $val);
                        if ($row[0] != '<?PHP exit;?>')
                            continue;
                        if ($row[3] == $hash && ($row[1] > $time - $maxtime)) {
                            return;
                        }
                    }
            }
        }
        error_log($message, 3, $file);
    }

}
