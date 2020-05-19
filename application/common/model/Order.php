<?php


namespace app\common\model;
use \think\Model;
class Order extends Model {

	protected $pk = 'id';
	protected $table = 'order';
	protected $autoWriteTimestamp = true;


}

?>