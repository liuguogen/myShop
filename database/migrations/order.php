<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class order extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `member_id` int(11) not null comment '用户id',
  `name` varchar(50) not null comment '姓名',
  `mobile` varchar(20) not null comment '手机号',
  `province` varchar(100) not null comment '省',
  `city` varchar(100) not null comment '市',
  `area` varchar(100) not null comment '区',
  `address` varchar(200) not null comment '详细地址',
  `memo` longtext comment '订单备注',
  `create_time` int(11) not null comment '创建时间',
  `update_time` int(11) not null comment '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `index_order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '订单主表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "order" );
    }
}