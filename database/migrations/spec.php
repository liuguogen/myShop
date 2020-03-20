<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class spec extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `spec` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `spec_name` varchar(60) NOT NULL COMMENT '规格名称',
  `spec_alias` varchar(100) NOT NULL DEFAULT '' COMMENT '规格别名',
  `spec_memo` varchar(100) NOT NULL DEFAULT '' COMMENT '规格备注',
  `disabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_spec_name` (`spec_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT = '规格表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "spec" );
    }
}