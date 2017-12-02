<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * AppPack模块
 * C::m('apppack')->fun();
 **/
class module_apppack
{
    // pack应用包zip
    public function pack_app_zip($rid,$zipfile,$version='')
    {
        //1. 获取pack info
        $appPackInfo = C::t('dzapp_pack')->getByPk($rid);
        if (empty($appPackInfo)) {
            throw new Exception("该应用版本不存在或已删除[$rid]");
        }
        $appkey = $appPackInfo['appkey'];

        //2. unzip
        $appVersion = $appPackInfo['version'];
        require_once libfile('function/addon');
        $tmpdir = DISCUZ_ROOT . '/data/tmp_' . random(5);
        $filename = $zipfile;
        include_once libfile('class/pclzip');
        $zip = new PclZip($filename);
        dmkdir($tmpdir);
        $unzip = $zip->extract(PCLZIP_OPT_PATH, $tmpdir);
        if (!is_dir("$tmpdir/$appkey")) {
            dir_clear($tmpdir);
            unlink($zipfile);
            throw new Exception('zip解压后找不到'.$appkey.'目录');
        }

        //3. pack
        $_ENV['item'] = array('key'=>$appkey);
        $_ENV['revision'] = array (
            'id'       => $rid,
            'version'  => $appVersion,
            'dateline' => TIMESTAMP,
        );
        $plugindir = $tmpdir . "/" . $_ENV['item']['key'];
        revision_xml_create($plugindir, 1, $_ENV['item'], $_ENV['revision']);
        revision_xml($plugindir);
        revision_xml_save($_ENV['item']['key'], $_ENV['revision']['id']);

        //4. finish
        dir_clear($tmpdir);
        C::t('dzapp_pack')->updateStatus($rid,0,$version);   //!< 打包成功后状态置为上架
    }

}

?>
