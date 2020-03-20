<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class role extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL COMMENT '角色名称',
  `menu_data` longtext NOT NULL COMMENT '菜单列表',
  `role_params` longtext NOT NULL COMMENT '原始数据',
  `role_desc` longtext NOT NULL COMMENT '权限描述',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `index_role_name` (`role_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT = '管理员权限表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "role" );
    }
}