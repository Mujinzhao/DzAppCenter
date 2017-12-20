<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
include_once libfile('function/member');
$actionlist = array(
    'login' => array(),
    'logout' => array(),
);
$action = isset($_GET['action']) ? $_GET['action'] : "query";
try {
    $fun = $action."Action";
    if (!isset($actionlist[$action]) || !function_exists($fun)) {
        throw new Exception('unknow action');
    }
    $res = $fun();
    api_result(array("data"=>$res));
} catch (Exception $e) {
    api_result(array('retcode'=>100010,'retmsg'=>$e->getMessage()));
}

// 用户登录
function loginAction()
{/*{{{*/
    global $_G;
    if ($_G['uid']) {
        return $_G['member'];
    }
    $username = validate::getNCParameter('username','username','string',15);
    $password = validate::getNCParameter('userpass','userpass','string',32);
    return userlogin($username,$password);
}/*}}}*/

// 退出登录
function logoutAction()
{/*{{{*/
    clearcookies();
    return "你已退出登录";
}/*}}}*/


