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
  `order_status` enum('create','cancel','finish') not null default 'create' comment '订单状态 订单创建  订单取消 订单完成',
  `pay_status` enum('wait','already','finish','cancel')  not null default 'wait' comment '支付状态 等待支付 已支付 支付完成 支付取消',
  `delivery_status` enum('wait','shipped','confirm','finish','cancel') not null default 'wait' comment '发货状态 未发货 已发货 确认收货 完成 取消',
  `amount` decimal(10,2) not null default 0 comment '订单总金额',
  `pay_amount` decimal(10,2) not null default 0 comment '支付金额',
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
  KEY `index_order_no` (`order_no`),
  KEY `index_order_status` (`order_status`),
  KEY `index_pay_status` (`pay_status`)
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