<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
class table_dzapp extends DB
{
    public function __construct() {
        $this->_table = 'dzapp';
        $this->_pk    = 'appid';
        $this->_pre_cache_key = 'dzapp_';
    }

    public function getByPk($pk) {
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$pk'";
        return DB::fetch_first($sql);
    }

    public function getByAppKey($appkey) {
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE appkey='$appkey'";
        return DB::fetch_first($sql);
    }


    // 查询
    public function queryApplist()
    {/*{{{*/
        $return = array(
            "totalProperty" => 0,
            "root" => array(),
        );
        $key     = validate::getNCParameter('key','key','string',128);
        $apptype = validate::getOPParameter('apptype','apptype','string',128,'');
        $sort    = validate::getOPParameter('sort','sort','string',128,'ctime');
        $dir     = validate::getOPParameter('dir','dir','string',128,'DESC');
        $start   = validate::getOPParameter('start','start','integer',1024,0);
        $limit   = validate::getOPParameter('limit','limit','integer',1024,20);
        $where   = "a.isdel=0 AND a.status=0";
        if ($apptype=='plugin') $where.=" AND a.apptype='plugin'";
        else if ($apptype=='template') $where.=" AND a.apptype='template'";
        if ($key!='') $where .= " AND (a.appkey like '%$key%' OR a.appname like '%$key%')";
        $table_dzapp = DB::table('dzapp');
        $sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*
FROM $table_dzapp as a
WHERE $where
ORDER BY $sort $dir
LIMIT $start,$limit
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        return $return;
    }/*}}}*/

    // 保存APP
    public function saveApp()
    {/*{{{*/
        $appid   = validate::getNCParameter('appid','appid','integer');
        $appkey  = validate::getNCParameter('appkey','appkey','string',128);
        $appInfo = $this->getByAppKey($appkey);
        if (!empty($appInfo) && $appid!=$appInfo['appid']) {
            throw new Exception("$appkey 已存在");
        }
        $data = array (
            'appkey'  => $appkey,
            'appname' => validate::getNCParameter('appname','appname','string',64),
            'appdesc' => validate::getNCParameter('appdesc','appdesc','string',1024),
            'apptype' => validate::getOPParameter('apptype','apptype','string',128,'plugin'),
            'author'  => validate::getNCParameter('author','author','string',64),
        );
        if ($appid==0) {
            return $this->insert($this->_table,$data);
        } else {
            return $this->update($this->_table,$data,"appid=$appid");
        }
    }/*}}}*/
    

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
