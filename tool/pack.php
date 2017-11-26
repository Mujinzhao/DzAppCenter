#!/usr/bin/env php
<?php
/***********************************************************
 * @file:   pack.php
 * @author: mawentao
 * @create: 2017-11-24 16:51:31
 * @modify: 2017-11-24 16:51:31
 * @brief:  discuz app 打包工具
 *     将zip包打包成AppCenter的pack
 ***********************************************************/

define('CURDIR',dirname(__FILE__));

function init_discuz_context() {
    require_once(CURDIR.'/../source/class/class_core.php');
    $core = &core::instance();
    $core->init();
    ini_set('date.timezone','Asia/Shanghai');
}

function pack_app_zip($appkey,$rid,$zipfile)
{
    //1. 获取pack info
    $appPackInfo = C::t('dzapp_pack')->getByPk($rid);
    if ($appPackInfo['appkey']!=$appkey) {
        throw new Exception("dzapp_pack[rid:$rid]'s appkey is ".$appPackInfo['appkey']." not $appkey");
    }

    //2. unzip
    $appVersion = $appPackInfo['version'];
    require_once libfile('function/addon');
    $tmpdir = CURDIR . '/data/tmp_' . random(5);
    $filename = $zipfile;
    include_once libfile('class/pclzip');
    $zip = new PclZip($filename);
    dmkdir($tmpdir);
    $unzip = $zip->extract(PCLZIP_OPT_PATH, $tmpdir);

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
    C::t('dzapp_pack')->updateStatus($rid,0);   //!< 打包成功后状态置为上架
    echo "PACK SUCCESS: ".DISCUZ_ROOT."pack/$appkey/$rid [version:$appVersion]\n";
}

if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME']))
{
    error_reporting(E_ALL);
    //1. check args
    if (!isset($argv[1]) || $argv[1]=="") {
        $exe = $argv[0];
        echo "[usage]: php $exe zipfile\n";
        exit(0);
    }
    define('ZIPFILE', $argv[1]);
    //2. init dz
    init_discuz_context();
    //3. parse app
    try {
        $zipfile = ZIPFILE;
        $arr = explode(DIRECTORY_SEPARATOR,$zipfile);
        $appkey = $arr[2];
        $rid = $arr[3];
        if (!is_numeric($rid)) {
            throw "unknow rid [$rid]";        
        }
        pack_app_zip($appkey,$rid,$zipfile);
    } catch (Exception $e) {
        $log = $e->getMessage();
        die("[ERROR]: $log\n");
    }
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
