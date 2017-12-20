<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 *     [Discuz!] (C)2001-2099 Comsenz Inc.
 *     $Id: function_member.php  2017-12-20 16:48  mawentao
 **/

/**
 * 用户注册
 **/
function userregister($username,$email,$password)
{
    return C::t('common_member')->register($username,$email,$password);   
}


/**
 * 用户登录校验
 **/
function userlogin($username, $password)
{
    $member = C::t('common_member')->loginCheck($username, $password);
    if (empty($member)) {
        throw new Exception("用户名不存在或密码错误");
    }
    setloginstatus($member, 86400);
    return $member;
}

/**
 * 设置member登录
 **/
function setloginstatus($member, $cookietime) 
{
    global $_G;
    $_G['uid'] = intval($member['uid']);
    $_G['username'] = $member['username'];
    $_G['member'] = $member;
    dsetcookie('auth', authcode("{$member['password']}\t{$member['uid']}", 'ENCODE'), $cookietime, 1, true);
}


