<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * AppLogo模块
 * C::m('applogo')->fun();
 **/
class module_applogo
{
    public function getLogoInfo($appkey)
    {
        global $_G;
        $img_dir = DISCUZ_ROOT."/resource/plugin";
        if (!is_dir($img_dir)) { mkdir($img_dir,0777,true); }
        return array (
            'img_file' => DISCUZ_ROOT."resource/plugin/".$appkey.".png",
            'img_url'  => $_G['siteurl']."resource/plugin/".$appkey.".png",
        );
    }
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
