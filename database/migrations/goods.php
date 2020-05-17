<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class goods extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '商品名称',
  `cate_id` int(11) not null comment '商品分类id',
  `brand_id` int(11) not null comment '品牌id',
  `type_id` int(11) not null comment '商品类型id',
  `sku_type` enum('signle','many') not null default 'signle' comment 'sku类型 signle 单规格,many 多规格',
  `bn` varchar(50) not null comment '商品货号',
  `price` decimal(10,2) not null default 0 comment '商品价格',
  `mkt_price` decimal(10,2) not null default 0 comment '参考价',
  `weight` decimal(10,3) not null default 0 comment '重量' ,
  `store` int(11) not null default 0 comment '库存',
  `sales_status` tinyint(1) not null default 0 comment '是否上架 0 否 1 是',
  `store_type` tinyint(1) not null default 1 comment '库存计算方式 1 下单减 2 付款减',
  `goods_img` varchar(200) not null comment '商品主图',
  `thumb` text default '' comment '商品缩略图',
  `goods_desc` longtext default '' comment '商品介绍',
  `create_time` int(11) not null comment '创建时间',
  `update_time` int(11) not null comment '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `index_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '商品主表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "goods" );
    }
}