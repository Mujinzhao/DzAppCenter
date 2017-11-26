<?php
define('CURSCRIPT', 'index');

require_once('./source/class/class_core.php');
$core = &core::instance();
$core->init();

$modules = array (
    'plugin',
);
$mod = isset($_G['input']['mod']) ? $_G['input']['mod'] : '';
if(!in_array($mod, $modules)) {
    module_not_exists();
}
$version = !empty($_GET['version']) ? intval($_GET['version']) : 1;
if($version>4) $version=4;
while ($version>=1) {
    $apifile = DISCUZ_ROOT."source/api/$version/$mod.php";
    if(file_exists($apifile)) {
        require_once $apifile;
        exit(0);
    }
    --$version;
}
module_not_exists();

function module_not_exists()
{
    header("Content-type: application/json");
    echo json_encode(array('error' => 'module_not_exists'));
    exit;
}

function api_result(array $result,$json_header=true)
{
    header("Content-type: application/json");
    if (!isset($result['retcode'])) {
        $result['retcode'] = 0;
    }   
    if (!isset($result['retmsg'])) {
        $result['retmsg'] = 'succ';
    }   
    if ($json_header) {
        header("Content-type: application/json");
    }   
    echo json_encode($result);
    exit;
}

class validate
{
	// POST转义字符解码
	public static function post_decode($value) 
	{
		$value = htmlspecialchars_decode($value);
		$value = preg_replace("/&apos;/i", "'", $value);
		$value = preg_replace("/&lk;/i", "(", $value);
		$value = preg_replace("/&gk;/i", ")", $value);
		return $value;
	}

	/**
     * @brief 验证 REQUEST 中的必填字段
     * @param[in] $key : 字段key
     * @param[in] $name : 字段的含义
     * @param[in] $valueType :
	 *			字段值类型: integer, number, string, url, email
	 *          非以上值时，当成正则表达式处理
     * 
     **/
	public static function getNCParameter($key, $name, $valueType='string', $maxlen=1024)
	{
		//1. 检查字段是否设置
		if (!isset($_REQUEST[$key])) {
			$msg = $key." is not set.";
			throw new Exception($msg);
			return null;
		}

		//2. 去首尾空格
		$value = trim($_REQUEST[$key]);
		$value = self::post_decode($value); //!< 转义字符解码
		$_REQUEST[$key] = $value;

		//3. 根据数据类型检查
		$res = true;
		switch($valueType) {
			case "string"  : $res = self::checkString($value, $maxlen); break;
			case "number"  : $res = self::checkNumber($value); break;
			case "integer" : $res = self::checkInteger($value); break;
			case "url"     : $res = self::checkUrl($key, $maxlen); break;
			case "email"   : $res = self::checkEmail($key); break;
			default:
				if (preg_match($valueType, $value)) {
					$res = true;
				} else {
					$res = "格式不正确";
				}
				break;
		}

		//4. 检查失败抛异常
		if ($res !== true) {
			$msg = $name.$res;
			throw new Exception($msg);
		}
        return $_REQUEST[$key];
	}


	/**
     * @brief 验证 REQUEST 中的可选字段
     * @param[in] $key : 字段key
     * @param[in] 
     **/
	public static function getOPParameter($key, $name, $valueType='string', $maxlen=1024, $dafaultValue="")
	{
		if (!isset($_REQUEST[$key]) || $_REQUEST[$key]==="") {
			$_REQUEST[$key] = $dafaultValue;
			return $dafaultValue;
		}
		return self::getNCParameter($key, $name, $valueType, $maxlen);
	}

	// 验证字符串
	private static function checkString($str_utf8, $maxlen)
	{/*{{{*/

		if (mb_strlen($str_utf8, "UTF-8") > $maxlen) {
			return "不能超过".$maxlen."个字";
		}
		$illegalCharacters = array('delete', 'null', '||');
		foreach ($illegalCharacters as &$wd) {
			if (stristr($str_utf8, $wd)) {
				return "不能包含字符 $wd";
			}
		}
		return true;
	}/*}}}*/

	// 验证数字
	private static function checkNumber($value)
	{/*{{{*/
		if (!is_numeric($value)) {
			return "必须是数字";
		}
		return true;
	}/*}}}*/

	// 验证整数
	private static function checkInteger($value)
	{/*{{{*/
		$regex = "/^-?\d+$/";
		if (!preg_match($regex, $value)) {
			return "必须是整数";			
		}
		return true;
	}/*}}}*/

	// 验证url
	private static function checkUrl($key, $maxlen)
	{/*{{{*/
		$value = trim($_REQUEST[$key]);
		$res = self::checkString($value, $maxlen);
		if ($res !== true) {
			return $res;
		}
		$regex = "/^(https?:\/\/)?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z_!~*'()-]+\.)*([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.[a-z]{2,6})(:[0-9]{1,4})?((\/?)|(\/[^\s]+)+\/?)$/i";
		if (!preg_match($regex, $value)) {
			return "不符合标准URL格式";
		}
		$initial = substr($value, 0, 4);
        if (strcmp($initial, "http") != 0) {
            $_REQUEST[$key] = "http://" . $value;
		}
		return true;
	}/*}}}*/

	// 验证email
	private static function checkEmail($value, $maxlen)
	{/*{{{*/
		$regex = "/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/";
		if (!preg_match($regex, $value)) {
			return "必须是Email";
		}
		return true;
	}/*}}}*/
}
// vim600: sw=4 ts=4 fdm=marker syn=php

