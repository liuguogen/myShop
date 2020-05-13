<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class widget extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `widget` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '模块名称',
  `title_desc` longtext  default null comment '描述',
  `banner_img` varchar(200) not null comment '宣传图片',
  `sub_title` varchar(200) not null default '' comment '副标题',
  `module_title` varchar(200) not null default '' comment '模块标题',
  `module_desc` longtext  default null comment '模块描述',
  `goods_id` varchar(200) not null comment '商品ID,以逗号分隔',
  `create_time` int(11) not null comment '创建时间',
  `update_time` int(11) not null comment '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `index_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '首页挂件表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "widget" );
    }
}