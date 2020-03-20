<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class goods_cate extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {

        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `goods_cate` (
	`id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `cate_name` VARCHAR(100) NOT NULL COMMENT '分类名称',
    `cate_img` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '分类图片',
    `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID',
    `disabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '启用状态 0：禁用； 1：正常',
	`create_time` INT(11) NOT NULL  COMMENT '数据创建时间',
	`update_time` INT(11) NOT NULL DEFAULT 0 COMMENT '最后更新时间',
	PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '商品分类表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "goods_cate" );
    }
}