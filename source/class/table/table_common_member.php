<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
class table_common_member extends DB
{
    public function __construct() {
        $this->_table = 'common_member';
        $this->_pk    = 'uid';
        $this->_pre_cache_key = 'member_';
    }

    public function getByPk($pk) {
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$pk'";
        return DB::fetch_first($sql);
    }

    // 注册用户
    public function register($username,$email,$password)
    {
        $data = array (
            'email'    => $email,
            'username' => $username,
            'password' => md5(md5($password).$username),
        );
        return $this->insert($this->_table,$data);
    }

    // 登录检查
    public function loginCheck($username,$password)
    {
        $password = md5(md5($password).$username);
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE username='$username' AND password='$password'";
        return DB::fetch_first($sql);
    }

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
