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

    // 查询
    public function queryApp($apptype,$key,$sort,$dir,$start,$limit=30)
    {
        $return = array(
            "totalProperty" => 0,
            "root" => array(),
        );
        $where = "a.isdel=0 AND a.status=0";
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
    }

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
