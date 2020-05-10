<?php

namespace app\common\model;
use \think\Model;

class Product extends Model {

	protected $pk = 'id';
	protected $table = 'product';
	protected $autoWriteTimestamp = true;


}

?>