<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class brand extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();    
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(60) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `brand_url` varchar(100) NOT NULL DEFAULT '' COMMENT '品牌地址',
  `brand_logo` varchar(100) NOT NULL DEFAULT '' COMMENT '品牌LOGO',
  `brand_desc` longtext NOT NULL COMMENT '品牌介绍',
  `brand_keywords` varchar(200) NOT NULL DEFAULT '' COMMENT '品牌关键字',
  `disabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '启用状态 0：禁用； 1：正常',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_brand_name` (`brand_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT = '商品品牌表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "brand" );
    }
}