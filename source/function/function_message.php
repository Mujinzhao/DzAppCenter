<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function dshowmessage($message, $url_forward = '', $waitSecond = '0') {

    if (!$url_forward) {
        $location_message = '<a href="javascript:history.back();">[ 点击这里返回上一页 ]</a>';
    } else {
        if ($waitSecond) {
            $waitSecond = $waitSecond ? $waitSecond : '3000';
            $location = '<script>setTimeout("window.location.href =\'' . $url_forward . '\';",' . $waitSecond . ');</script>';
        } else {
            $location_message = '<a href="' . $url_forward . '">如果您的浏览器没有自动跳转，请点击此链接</a>';
        }
        $location_message = '<a href="' . $url_forward . '">如果您的浏览器没有自动跳转，请点击此链接</a>';
    }

    include template('common/showmessage');
    dexit();
}
