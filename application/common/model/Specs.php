<?php

namespace app\common\model;
use \think\Model;
class Specs extends Model {

	protected $pk = 'id';
	protected $table = 'spec';
	protected $autoWriteTimestamp = true;
	public function spec_values()
    {
        return $this->hasMany('spec_values');
    }

}

?>