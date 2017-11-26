<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
class table_dzapp_pack extends DB
{
    public function __construct() {
        $this->_table = 'dzapp_pack';
        $this->_pk    = 'rid';
        $this->_pre_cache_key = 'dzapp_pack_';
    }

    public function getByPk($pk) {
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$pk'";
        return DB::fetch_first($sql);
    }

    // 更新状态
    public function updateStatus($rid,$status)
    {
        $data = array(
            'status' => $status,
            'lastpacktime' => date('Y-m-d H:i:s'),
        );
        $condition = array($this->_pk => $rid);
        return $this->update($this->_table,$data,$condition);
    }

    // 查询
    public function queryAppPack($apptype)
    {
        $return = array(
            "totalProperty" => 0,
            "root" => array(),
        );
        $sokey = validate::getNCParameter('key','key','string',128);
        $sort  = validate::getNCParameter('sort','sort','string',128);
        $dir   = validate::getOPParameter('dir','dir','string',1024,'DESC');
        $start = validate::getNCParameter('start','start','integer',1024,0);
        $limit = validate::getNCParameter('limit','limit','integer',1024,30);

        $where = "a.isdel=0 AND a.status=0";
        if ($apptype=='plugin' || $apptype=='template') $where.= " AND b.apptype='$apptype'";
        if ($sokey!='') $where.=" AND (b.appkey like '%$sokey%' OR b.appname like '%$sokey%')";

        $table_dzapp_pack = DB::table('dzapp_pack');
        $table_dzapp = DB::table('dzapp');
        $sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS 
a.*,b.appname,b.apptype,b.appdesc,b.author
FROM $table_dzapp_pack as a LEFT JOIN $table_dzapp as b ON a.appkey=b.appkey
WHERE $where
ORDER BY a.$sort $dir LIMIT $start,$limit
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        /////////////////////////////////////////////
        // 安装URL
        if (!empty($return["root"])) { 
            $siteurl = $_POST['params']['siteurl'];
            $siteuniqueid = $_POST['params']['siteuniqueid'];
            $urlpre = $siteurl."admin.php?action=cloudaddons&operation=download&";
            foreach ($return["root"] as &$row) {
                $addonids = $row['appkey'].'.'.$row['apptype'].'.'.$row['rid'];
                $sitekey = md5($addonids . md5($siteuniqueid.TIMESTAMP));
                $row['installUrl'] = $urlpre."addonids=$addonids&md5hash=$sitekey&timestamp=".TIMESTAMP;
            }
        }
        /////////////////////////////////////////////
        return $return;
    }
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
