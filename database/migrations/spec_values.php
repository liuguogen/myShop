<?php
namespace Console\Database\Migrations;

use Console\System\Migration;

class spec_values extends Migration
{
    /**
     * 创建数据表
     */
    public function up()
    {
        $this->down();
        $sql = <<<sql
CREATE TABLE
IF NOT EXISTS `spec_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `spec_id` int(11) unsigned NOT NULL COMMENT '关联规格ID',
  `spec_value` varchar(200) NOT NULL COMMENT '规格值名称',
  `spec_alias` varchar(100) NOT NULL DEFAULT '' COMMENT '规值别名',
  PRIMARY KEY (`id`),
  KEY `index_spec_value` (`spec_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '规格明细表';
sql;
        self::create( $sql );
    }

    /**
     * 删除数据表
     */
    public function down()
    {
        self::drop( "spec_values" );
    }
}