<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class order_sales extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `order_sales` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `goods_id` int(11) unsigned not null comment '主商品ID',
  `product_id` int(11) unsigned not null comment 'skuID',
  `free_num` int(11) unsigned not null default 0 comment '冻结库存',
  `sales_num` int(11) unsigned not null default 0 comment '销售数量',
  `create_time` int(11) not null comment '创建时间',
  `update_time` int(11) not null comment '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `index_order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '订单销售量及冻结库存表';
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