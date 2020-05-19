<?php


namespace app\common\model;
use \think\Model;
class OrderItem extends Model {

	protected $pk = 'id';
	protected $table = 'order_item';
	protected $autoWriteTimestamp = true;


}

?>