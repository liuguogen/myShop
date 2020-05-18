<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class order_item extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `order_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned not null comment '订单ID',
  `goods_id` int(11) unsigned not null comment '主商品ID',
  'product_id' int(11) unsigned not null comment 'skuID',
  `name` varchar(100) not null comment '商品名称',
  `create_time` int(11) not null comment '创建时间',
  `update_time` int(11) not null comment '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `index_type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '订单明细表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "order_item" );
    }
}