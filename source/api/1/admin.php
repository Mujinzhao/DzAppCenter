<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$actionlist = array(
    'queryApplist' => array(),
    'saveApp' => array(),
    'getApp' => array(),
    'queryApppacks' => array(),
    'saveApppack' => array(),
    'releaseApppack' => array(),
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

// 应用列表
function queryApplist(){ return C::t('dzapp')->queryApplist(); }

// 保存APP
function saveApp() { return C::t('dzapp')->saveApp(); }

// 获取APP信息
function getApp() { 
    $appkey  = validate::getNCParameter('appkey','appkey','string',128);
    return C::t('dzapp')->getByAppKey($appkey);
}

// 获取APP打包版本
function queryApppacks() {
    $appkey  = validate::getNCParameter('appkey','appkey','string',128);
    return C::t('dzapp_pack')->getAllByAppKey($appkey);
}

// 保存APP版本
function saveApppack() { return C::t('dzapp_pack')->saveAppPack();}

// 发布APP版本
function releaseApppack() 
{/*{{{*/
    try {
        $rid  = validate::getNCParameter('rid','rid','integer');
        //1. check apppack
        $packInfo = C::t('dzapp_pack')->getDetailByRid($rid);
        if (empty($packInfo) || $packInfo['isdel']!=0) {
            throw new Exception('应用版本不存在或已删除');
        }
        //2. 存储zip包
        $appkey = $packInfo['appkey'];
        $apptype = $packInfo['apptype'];
        $packversion = validate::getNCParameter('packversion','packversion','string');
        $file_dir = DISCUZ_ROOT."/tool/data/$apptype/$appkey/$rid";
        if (!is_dir($file_dir)) {
            mkdir($file_dir,0777,true);
        }
        $filename = $file_dir."/$appkey.$apptype.$rid-$packversion.zip";
        $zipfile = validate::getNCParameter('zipfile','zipfile','file',0);
        move_uploaded_file($zipfile['tmp_name'],$filename);
        //3. pack zip
        C::m('apppack')->pack_app_zip($rid,$filename,$packversion);
        $data = array (
            'zipfile' => $zipfile,
        );
        api_result($data,false);
    } catch (Exception $e) {
        api_result(array('retcode'=>100010,'retmsg'=>$e->getMessage()), false);
    }
}/*}}}*/



