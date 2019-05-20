<?php

namespace app\common\model;
use \think\Model;
class SpecValues extends Model {

	protected $pk = 'id';
	protected $table = 'spec_values';
	protected $autoWriteTimestamp = true;
	public function spec()
    {
        return $this->belongsTo('spec');
    }

}

?>