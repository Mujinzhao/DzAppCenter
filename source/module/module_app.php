<?php
$action = $_G['input']['ac'];

$rid = $_G['input']['rid'];
$appPack = C::t('dzapp_pack')->getByPk($rid);
if (!empty($appPack)) {
    $_ENV['item'] = array('key' => $appPack['appkey']);
    $_ENV['revision'] = array(
        'id'       => $appPack['rid'],
        'version'  => $appPack['version'],
        'dateline' => TIMESTAMP,
    );
}

/*
$plugin_apps = array (
    '100' => array (
        'id'  => '100',
        'key' => 'mobile',
        'version' => '1.0',
    ),
    '101' => array (
        'id'  => '101',
        'key' => 'demoapp',
        'version' => '2.0',
    ),
);

$plugin_app = $plugin_apps[$_G['input']['rid']];
if (!empty($plugin_app)) {
    $_ENV['item'] = array('key' => $plugin_app['key']);
    $_ENV['revision'] = array(
        'id' => $plugin_app['id'],
        'version' => $plugin_app['version'],
        'dateline' => TIMESTAMP,
    );
}
*/

//action
if ($_G['input']['ac'] == 'download') {
	require_once libfile('function/addon');
	revision_download();
	exit;
} elseif ($_G['input']['ac'] == 'installlog') {
	file_put_contents('data/install.log', print_r($_G['input'], 1), FILE_APPEND);
	exit;
} elseif ($_G['input']['ac'] == 'downloadlog') {
	file_put_contents('data/downloadlog.log', print_r($_G['input'], 1), FILE_APPEND);
	exit;
} elseif ($_G['input']['ac'] == 'faillog') {
	file_put_contents('data/faillog.log', print_r($_G['input'], 1), FILE_APPEND);
	exit;
} elseif ($_G['input']['ac'] == 'removelog') {
	file_put_contents('data/removelog.log', print_r($_G['input'], 1), FILE_APPEND);
	exit;
} elseif ($_G['input']['ac'] == 'validator') {
	file_put_contents('data/validator.log', print_r($_G['input'], 1), FILE_APPEND);
	exit;
} elseif ($_G['input']['ac'] == 'check') {
	file_put_contents('data/check.log', print_r($_G['input'], 1), FILE_APPEND);
	echo file_get_contents('md5/' . $_G['input']['file']);
	exit;
}

