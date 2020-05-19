<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class cart extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();    
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) unsigned not null comment '用户ID',
  `goods_id` int(11) unsigned not null comment '主商品ID',
  `product_id` int(11) unsigned not null comment 'skuID',
  `num` int(11) unsigned not null default 0 comment '商品数量',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_brand_name` (`brand_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT = '购物车表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "cart" );
    }
}