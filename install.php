<?php

define('CURSCRIPT', 'install');
require_once('./source/class/class_core.php');
$core = &core::instance();
$core->init();


extract($core->config['db'][1]);

echo <<<EOF
<h1>Discuz App Center Install...</h1>

DB CONFIG:<br>
dbhost: $dbhost <br>
dbname: $dbname

EOF;


$addtime = $modtime = date('Y-m-d H:i:s');

// dzapp主表
$table = DB::table('dzapp');
/*{{{*/
$sql = "CREATE TABLE IF NOT EXISTS $table ".<<<EOF
(
`appid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APP ID',
`appkey` varchar(64) NOT NULL DEFAULT '' COMMENT 'APP标识符(唯一键)',
`appname` varchar(64) NOT NULL DEFAULT '' COMMENT 'APP名称',
`apptype` varchar(8) NOT NULL DEFAULT 'plugin' COMMENT 'APP类型(plugin|template)',
`appdesc` varchar(64) NOT NULL DEFAULT '' COMMENT 'APP描述',
`author` varchar(64) NOT NULL DEFAULT '' COMMENT '作者',
`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态(0:上架,1:下架)',
`ctime` datetime NOT NULL DEFAULT "0000-00-00 00:00:00" comment '创建日期',
`mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
`isdel` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '删除标志',
PRIMARY KEY (`appid`),
UNIQUE KEY `uk_appkey` (`appkey`)
) ENGINE=MyISAM COMMENT 'dzapp主表'
EOF;
DB::query($sql);
$sql = "INSERT IGNORE INTO $table (appid,appkey,appname,apptype,appdesc,author,status,ctime) VALUES ".<<<EOF
(1,'mobile','掌上论坛','plugin','dz官方插件','Comsenz Inc.',0,'$addtime')
EOF;
DB::query($sql);
/*}}}*/

// dzapp_pack表
$table = DB::table('dzapp_pack');
/*{{{*/
$sql = "CREATE TABLE IF NOT EXISTS $table ".<<<EOF
(
`rid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APP PACK ID',
`appkey`  varchar(64) NOT NULL DEFAULT '' COMMENT 'APP标识符', 
`version` varchar(64) NOT NULL DEFAULT '' COMMENT '版本号',
`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态(0:上架,1:下架)',
`ctime` datetime NOT NULL DEFAULT "0000-00-00 00:00:00" comment '创建日期',
`mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
`lastpacktime` datetime NOT NULL DEFAULT "0000-00-00 00:00:00" comment '末次打包时间',
`isdel` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '删除标志',
PRIMARY KEY (`rid`),
KEY `idx_appkey` (`appkey`),
KEY `idx_status_isdel` (`status`,`isdel`)
) ENGINE=MyISAM COMMENT 'dzapp安装包表'
EOF;
DB::query($sql);
$sql = "INSERT IGNORE INTO $table (rid,appkey,version,status,ctime) VALUES ".<<<EOF
(100,'mobile','1.0','0','$addtime')
EOF;
DB::query($sql);
/*}}}*/

