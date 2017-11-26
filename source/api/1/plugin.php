<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$actionlist = array(
    'query' => array(),
);
$action = isset($_GET['action']) ? $_GET['action'] : "query";
try {
    if (!isset($actionlist[$action]) || !function_exists($action)) {
        throw new Exception('unknow action');
    }
    $res = $action();
    api_result(array("data"=>$res));
} catch (Exception $e) {
    api_result(array('retcode'=>100010,'retmsg'=>$e->getMessage()));
}

// 插件列表页查询
function query()
{
    /*
    $sokey = validate::getNCParameter('key','key','string',128);
    $sort  = validate::getNCParameter('sort','sort','string',128);
    $dir   = validate::getOPParameter('dir','dir','string',1024,'DESC');
    $start = validate::getOPParameter('start','start','integer',1024,0);
    return C::t('dzapp')->queryApp('plugin',$sokey,$sort,$dir,$start);
    */
    return C::t('dzapp_pack')->queryAppPack('plugin');
}

