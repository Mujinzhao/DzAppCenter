<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function dmkdir($dir, $mode = 0777){
	if(!is_dir($dir)) {
		dmkdir(dirname($dir));
		@mkdir($dir, $mode);
	}
	return true;
}

function dir_clear($dir) {
	if($directory = @dir($dir)) {
		while($entry = $directory->read()) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$filename = $dir.'/'.$entry;
			if(is_file($filename)) {
				@unlink($filename);
			} else {
				dir_clear($filename);
			}
		}
		$directory->close();
		@rmdir($dir);
	}
}

function revision_xml_create($dir, $packnum, $item, $revision) {
    global $_G;
    $_ENV['developer']['xmlarray'][$packnum] = array(
        'Title' => 'Discuz! File Pack',
        'Version' => '1',
        'Date' => TIMESTAMP,
        'Data' => array(
            'part' => '',
            'type' => 'plugin',
            'key' => $item['key'],
            'dateline' => $revision['dateline'],
            'version' => $revision['version']
            ),
    );
    if ($packnum == 1) {
        $_ENV['developer']['basedir'] = $dir . '/';
        $_ENV['developer']['md5'] = '';
        $_ENV['developer']['continue'] = array();
    }
    $_ENV['developer']['packnum'] = $packnum;
    $_ENV['developer']['item'] = $item;
    $_ENV['developer']['revision'] = $revision;
    $_ENV['developer']['size'] = 0;
}

function revision_xml($dir) {
    static $maxlength = 1024000;
    $dh = opendir($dir);
    while (($file = readdir($dh)) !== false) {
        if ($file != '.' && $file != '..') {
            $curfile = $dir . '/' . $file;
            if (is_file($curfile)) {
                $basename = str_replace($_ENV['developer']['basedir'], '', $curfile);
                $content = file_get_contents($curfile);
                $md5 = md5($content);
                $length = strlen($content);
                if ($_ENV['developer']['size'] + $length > $maxlength) {
                    $l = $_ENV['developer']['size'] + $length - $maxlength;
                    $i = 0;
                    while ($l > 0) {
                        $thiscontent = substr($content, 0, $length - $l);
                        $content = substr($content, $length - $l);
                        $thiscontent = base64_encode(gzcompress($thiscontent, 9));
                        $l = strlen($content);
                        $_ENV['developer']['xmlarray'][$_ENV['developer']['packnum']]['Data']['files'][$basename]['Data'] = $thiscontent;
                        if (!$i) {
                            if ($varexist) {
                                $_ENV['developer']['xmlarray'][$_ENV['developer']['packnum']]['Data']['files'][$basename]['VAR'] = 1;
                                $md5 = '';
                            } else {
                                $_ENV['developer']['xmlarray'][$_ENV['developer']['packnum']]['Data']['files'][$basename]['MD5'] = $md5;
                            }
                        }
                        $_ENV['developer']['xmlarray'][$_ENV['developer']['packnum']]['Data']['files'][$basename]['Part'] = ++$i;
                        if ($l > 0) {
                            revision_xml_create('', $_ENV['developer']['packnum'] + 1, $_ENV['developer']['item'], $_ENV['developer']['revision']);
                        }
                    }
                } else {
                    $length = strlen($content);
                    $content = base64_encode(gzcompress($content, 9));
                    $_ENV['developer']['xmlarray'][$_ENV['developer']['packnum']]['Data']['files'][$basename]['Data'] = $content;
                    $_ENV['developer']['xmlarray'][$_ENV['developer']['packnum']]['Data']['files'][$basename]['MD5'] = $md5;
                }
                $_ENV['developer']['md5'] .= $md5;
                $_ENV['developer']['size'] += $length;
                if ($_ENV['developer']['size'] >= $maxlength) {
                    revision_xml_create('', $_ENV['developer']['packnum'] + 1, $_ENV['developer']['item'], $_ENV['developer']['revision']);
                }
                if ($_ENV['developer']['continue']) {
                    break;
                }
            } else {
                revision_xml($curfile);
            }
        }
    }
}

function revision_xml_save($key, $id) {
    $filename = $key . '_' . $id;
    require_once libfile('class/xml');
    dir_clear(DISCUZ_ROOT . './pack/' . $key . '/' . $id);
    dmkdir(DISCUZ_ROOT . './pack/' . $key . '/' . $id);
    for ($i = 1; $i <= $_ENV['developer']['packnum']; $i++) {
        if (!$_ENV['developer']['xmlarray'][$i]['Data']['files']) {
            continue;
        }
        $fp = fopen(DISCUZ_ROOT . './pack/' . $key . '/' . $id . '/' . $filename . '.' . $i . '.xml', 'wb');
        $_ENV['developer']['xmlarray'][$i]['Data']['part'] = $i . '/' . $_ENV['developer']['packnum'];
        fwrite($fp, array2xml($_ENV['developer']['xmlarray'][$i]));
        fclose($fp);
    }
    $fp = fopen(DISCUZ_ROOT . './md5/' . $filename, 'wb');
    fwrite($fp, md5($_ENV['developer']['md5']));
    fclose($fp);
}

function revision_download() {
    global $_G;
//    if(!$_G['sitemaster']['id']) {
//        app::xml_message(array('Status' => 'Error', 'ErrorCode' => '&#24212;&#29992;&#20316;&#32773;&#31105;&#27490;&#24744;&#19979;&#36733;'));//应用作者禁止您下载
//    }
//    $revision = DB::fetch_first("SELECT * FROM ".DB::table('item_revision')." WHERE id='$_G[gp_rid]' AND packed>'0'");
//    if(!$revision || $revision['type'] == 2 && !$downpack) {
//        app::xml_message(array('Status' => 'Error', 'ErrorCode' => '&#24212;&#29992;&#30340;&#29256;&#26412;&#19981;&#23384;&#22312;'));//应用的版本不存在
//    }
//    $item = DB::fetch_first("SELECT * FROM ".DB::table('item')." WHERE id='$revision[id_item]' AND `status`>'0'");
//    if(!$item) {
//        app::xml_message(array('Status' => 'Error', 'ErrorCode' => '&#24212;&#29992;&#19981;&#23384;&#22312;'));//应用不存在
//    }
//    $developer = DB::fetch_first("SELECT * FROM ".DB::table('developer')." WHERE id='$item[id_developer]' AND `status`>='0'");
//    if(!$developer) {
//        app::xml_message(array('Status' => 'Error', 'ErrorCode' => '&#24212;&#29992;&#20316;&#32773;&#19981;&#23384;&#22312;'));//应用作者不存在
//    }

//    $download = DB::fetch_first("SELECT * FROM ".DB::table('download')." WHERE id_sitemaster='".$_G['sitemaster']['id']."' AND id_revision='$_G[gp_rid]'");
//    if($download['status'] == -2) {
//        app::xml_message(array('Status' => 'Error', 'ErrorCode' => '&#24212;&#29992;&#20316;&#32773;&#31105;&#27490;&#24744;&#19979;&#36733;'));//应用作者禁止您下载
//    }
//    if($revision['method'] == 2 && !$download['ispayed']) {
//        app::xml_message(array('Status' => 'Error', 'ErrorCode' => '&#35831;&#36141;&#20080;&#21518;&#20877;&#19979;&#36733;'));//请购买后再下载
//    }
    require_once libfile('class/xml');

    $packnum = intval($_G['input']['packnum']) + 1;
    $filename = DISCUZ_ROOT.'./pack/'.$_ENV['item']['key'].'/'.$_ENV['revision']['id'].'/'.$_ENV['item']['key'].'_'.$_ENV['revision']['id'].'.'.$packnum.'.xml';
    if(file_exists($filename)) {
        $array = xml2array(file_get_contents($filename));
        echo array2xml($array);
        exit;
    } else {
        xml_message(array(
            'Status' => 'End',
            'ID' => $_ENV['item']['key'].'.plugin',
            'SN' => $sn,
            'RevisionID' => $_ENV['revision']['id'],
            'RevisionDateline' => $_ENV['revision']['dateline']));
    }
}

function xml_message($data) {
    $array = array(
        'Title' => 'Discuz! File Pack',
        'Version' => '1',
        'Type' => 'addon',
        'Data' => $data
    );
    require_once libfile('class/xml');
    echo array2xml($array);
    exit;
}
