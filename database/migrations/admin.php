<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class admin extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码',
  `is_super` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是超管',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '用户状态 0：禁用； 1：正常 ；2：未验证',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `last_login_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `is_disabled` int(11) NOT NULL DEFAULT '1' COMMENT '是否启用1 0否',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色管关联ID',
  PRIMARY KEY (`id`),
  KEY `index_username` (`username`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT = '管理员表';
insert into admin (username,password,is_super,status) values('admin','$2a$08$yhE1JadHznr3AU3zA7zNjefqiJcF3ARB8zARUo4kH1jdSha7n6l8m',1,1);
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "admin" );
    }
}