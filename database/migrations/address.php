<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class address extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();    
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) unsigned not null comment '会员ID',
  `name` varchar(100) not null comment '姓名',
  `mobile` varchar(20) not null comment '手机号',
  `province` varchar(100) not null comment '省',
  `city` varchar(100) not null comment '市',
  `area` varchar(100) not null comment '区',
  `address` varchar(200) not null comment '详细地址',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_name` (`name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT = '会员地址表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "address" );
    }
}