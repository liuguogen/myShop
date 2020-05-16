<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class member extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE `member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) not null default '' comment '用户名',
  `pwd` varchar(64) not null default '' COMMENT '密码',
  `openid` varchar(200) not null default '' comment '微信openid',
  `avatar` varchar(100) not null default '' comment '图像',
  `age` int(10) not null default 0 comment '年龄',
  `birthday` varchar(20) not null default '' comment '生日',
  `member_type` enum('mobile','wechat','program','browser') not null default  'program' comment '账户来源类型',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "member" );
    }
}