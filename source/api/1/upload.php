<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$actionlist = array(
    'applogo' => array(),   //!< 上传applogo
);
$action = isset($_GET['action']) ? $_GET['action'] : "query";
try {
    if (!isset($actionlist[$action]) || !function_exists($action)) {
        throw new Exception('unknow action');
    }
    $res = $action();
    api_result(array("data"=>$res),false);
} catch (Exception $e) {
    api_result(array('retcode'=>100010,'retmsg'=>$e->getMessage()));
}

// 上传applogo
function applogo()
{
    global $_G;
    //1. 上传文件
    $imgcfg = array('width'=>0, 'height'=>0, 'size'=>102400000);
    $fileid  = validate::getNCParameter("fileElementId","fileElementId","string");
    $upfile  = get_upload_file($fileid);
    check_upload_img($upfile, $imgcfg);
    $tmpFile  = $upfile['tmp_name'];
    $fileSize = $upfile['size'];

    //2. 准备文件路径和文件下载地址
    $appkey = validate::getNCParameter("appkey","appkey","string");
    $appLogoInfo = C::m('applogo')->getLogoInfo($appkey);
    $img_file = $appLogoInfo['img_file'];
    $img_down = $appLogoInfo['img_url'];
    //$img_dir = DISCUZ_ROOT."/resource/plugin";
    //if (!is_dir($img_dir)) { mkdir($img_dir,0777,true); }
    //$img_file = DISCUZ_ROOT."resource/plugin/".$appkey.".png";
    //$img_down = $_G['siteurl']."resource/plugin/".$appkey.".png";
    if (is_file($img_file)) unlink($img_file);

    //3. 存储
    move_uploaded_file($tmpFile,$img_file);
    $res = array (
        'imgurl' => $img_down,
    );  
    return $res;
}

// 获取上传的文件
function get_upload_file($fileid)
{/*{{{*/
    $upfile = $_FILES[$fileid];
    if ($upfile["error"]!==0) {
        $err = $upfile["error"];
        $errMap = array(
            '1' => '文件大小超出服务器空间大小',
            '2' => '文件超出浏览器限制大小',
            '3' => '文件仅部分被上传',
            '4' => '未找到要上传的文件',
            '5' => '服务器临时文件丢失',
            '6' => '文件写入到临时文件出错',
        );
        $errMsg = isset($errMap[$err]) ? $errMap[$err] : "文件未上传或上传失败";
        throw new Exception($errMsg);
    }  
    return $upfile;
}/*}}}*/

// 上传文件校验
function check_upload_img($upfile, $imgcfg)
{/*{{{*/
    $tmpFile  = $upfile['tmp_name'];
    $fileSize = $upfile['size'];
    $imginfo = @getimagesize($tmpFile);
    if (false===$imginfo) {
        throw new Exception('请上传图片文件');
    }
    $width  = $imgcfg["width"];
    $height = $imgcfg["height"];
    $size   = $imgcfg["size"];
    if ($width!=0 && $height!=0) {
        if ($width!=$imginfo[0] || $height!=$imginfo[1]) {
            throw new Exception("请上传 ".$width."x".$height." 的图片文件");
        }
    }
    if ($fileSize>$size) {
        throw new Exception("图片大小不得超过"+get_file_size_string($size));
    }
}/*}}}*/

