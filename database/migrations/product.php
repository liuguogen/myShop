<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class product extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_id` int(11) not null comment '商品ID',
  `bn` varchar(50) not null comment '商品货号',
  `price` decimal(10,4) not null default 0 comment '商品价格',
  `mkt_price` decimal(10,4) not null default 0 comment '参考价',
  `weight` decimal(10,3) not null default 0 comment '重量' ,
  `store` int(11) not null default 0 comment '库存',
  `spece_value` varchar(200) not null default '' comment '规格值',
  `sales_status` tinyint(1) not null default 0 comment '是否上架 0 否 1 是',
  `create_time` int(11) not null comment '创建时间',
  `update_time` int(11) not null comment '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `index_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '商品规格表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "product" );
    }
}