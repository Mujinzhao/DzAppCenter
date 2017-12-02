<?php

define('CURSCRIPT', 'index');

require_once('./source/class/class_core.php');
$core = &core::instance();

$modarray = array('dashboard','addGroup','app');
$mod = in_array($_G['input']['mod'], $modarray) ? $_G['input']['mod'] : 'index';
if (isset($_G['input']['ac']) && $mod!='app') 
    $mod='app';
define('CURMODULE', $mod);

$core->init();

/////////////////////////////////////////////////////////////////////////////////
// 获取应用图标
if (count($_G['input'])==1) {
    $keys = array_keys($_G['input']);
    $appKey = $keys[0];
    if ($_G['input'][$appKey]=='' && strpos($appKey,'_')===0) {
        $appKey = substr($appKey,1);
        $url = "http://addon.discuz.com/?_$appKey";
        // 如果本地上传了优先用本地上传的
        $appLogoInfo = C::m('applogo')->getLogoInfo($appKey);
        if (is_file($appLogoInfo['img_file'])) {
            $url = $appLogoInfo['img_url'];
        }
        header("Location:$url");
        exit(0);
    }
}
/////////////////////////////////////////////////////////////////////////////////

//通用参数
if (!empty($_G['input']['data']) && !empty($_G['input']['md5hash']) && !empty($_G['input']['timestamp'])) {
	$data = base64_decode($_G['input']['data']);
	if (substr(md5($data . $_G['input']['timestamp']), 8, 8) == $_G['input']['md5hash'] && TIMESTAMP - $_G['input']['timestamp'] < 86400) {
		parse_str($data, $var);
//        print_r($var);
	} else {
		die('Error');
	}
}

require DISCUZ_ROOT.'./source/module/module_'.CURMODULE.'.php';
