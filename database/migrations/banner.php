<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class banner extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();    
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `banner_name` varchar(60) NOT NULL DEFAULT '' COMMENT '图片名称',
  `image` varchar(200) NOT NULL DEFAULT '' COMMENT '图片地址',
  `goods_id` varchar(200) NOT NULL DEFAULT '' COMMENT '商品ID 多个以,分割',
  `disabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_banner_name` (`banner_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT = '轮播图表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "banner" );
    }
}