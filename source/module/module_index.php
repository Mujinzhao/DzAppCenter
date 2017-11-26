<?php
$siteurl = $var['siteurl'];
$siteuniqueid = $var['siteuniqueid'];
$installUrlPre = $var['siteurl'].'admin.php?action=cloudaddons&operation=download&';

include template('index');


/*
// 插件应用列表
$plugin_apps = array (
    array (
        'addonids' => 'mobile.plugin.100',
    ),
    array (
        'addonids' => 'demoapp.plugin.101',
    ),
);


foreach ($plugin_apps as $plugin_app) {
    $addonids = $plugin_app['addonids'];
    $sitekey = md5($addonids . md5($var['siteuniqueid'] . TIMESTAMP));
    $url = $var['siteurl'] . 'admin.php?action=cloudaddons&operation=download&addonids=' . $addonids . '&md5hash='. $sitekey . '&timestamp=' . TIMESTAMP;
    echo '<a href="' . $url . '">' . $url . '</a><br/><br/>';
} 

/*
//下载
$addonids = 'mobile.plugin.100';
$sitekey = md5($addonids . md5($var['siteuniqueid'] . TIMESTAMP));
$url = $var['siteurl'] . 'admin.php?action=cloudaddons&operation=download&addonids=' . $addonids . '&md5hash=' . $sitekey . '&timestamp=' . TIMESTAMP;
echo '<a href="' . $url . '">' . $url . '</a><br/><br/>';

echo '<a href="?pack=1">Pack</a>';

*/
