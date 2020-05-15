<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// |  liuguogen <liuguogen_vip@163.com>
// +----------------------------------------------------------------------
namespace api\v1\admin\goods;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\GoodsType;
use \model\Goods;
use \model\Specs;
use \model\SpecValues;
/**
 * 
 */
class type
{




	public function __construct()
	{
		$this->goodsTypeMdl = model('GoodsType');
		$this->goodsMdl = model('Goods');
		$this->specMdl = model('Specs');
		$this->specValueMdl = model('SpecValues');
	}
	/**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        
        $return =  [
        	//['field'=>'accessToken','type'=>'string','valid'=>'require','desc'=>'accessToken','example'=>''],
        	['field'=>'page','type'=>'number','valid'=>'','desc'=>'页码','example'=>'1'],
        	['field'=>'limit','type'=>'number','valid'=>'','desc'=>'偏移量','example'=>'10'],
        	
        ];
        return $return;
    }


    private function getField() {
    	 $return['field'] =   [
    		['field'=>'id','title'=>'编号','width'=>'80','sort'=>true,'fixed'=>'left'],
    		['field'=>'type_name','title'=>'类型名称','width'=>'80','sort'=>false,'fixed'=>''],
    		
    	];

    	return $return;
    }


    public function get(array $params) {
    	$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		unset($params['limit'],$params['page']);
		$typeList['count'] = $this->goodsTypeMdl->where(array_filter($params))->count();
		
		$typeList['data'] = $this->goodsTypeMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		
		
		return $typeList;
    }
    /**
     * 保存类型
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {
    	
    	$validate = new Validate([
		    
		    'type_name' => 'require',
		    'spec_id'=>'require',
		],[
			'type_name.require'=>'类型名称必填',
			'spec_id.require'=>'规格必填',
		]);

		$check_data = [
		   
		    'type_name' => $params['type_name'],
		    'spec_id' => $params['spec_id'],
		];

		
		
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$data = [
			'type_name'=>trim($params['type_name']),
			'spec_id'=>is_array($params['spec_id']) ? implode(',', $params['spec_id']) : $params['spec_id'],
			'disabled'=>intval($params['disabled']),
			'create_time'=>time(),
			'update_time'=>time(),
		];
		
		if(isset($params['id']) && $params['id']) {
			$flag = $this->goodsTypeMdl->where(['id'=>intval($params['id'])])->update($data);
		}else {
			$flag = $this->goodsTypeMdl->save($data);
		}
		
		if(!$flag) {
			throw new HttpException(404,'保存失败！');
		}
		return ['data'=>$flag];
		
    }

    /**
     * 获取单条数据
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function getRow(array $params) {
    	$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$id = intval($params['id']);
		unset($params['id']);
		$rs = $this->goodsTypeMdl->where(['id'=>$id])->find();
		
		if(!$rs) throw new HttpException(404,'获取失败');
		$data['data'] = $rs;
		$data['data']['spec_id'] = $rs['spec_id'] ? explode(',', $rs['spec_id']) : [];
		
		return $data;
    }
	
	/**
	 * 更新数据
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function update(array $params)
	{


		$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		if($params['id'] && is_array($params['id'])) {
			$ids  = $params['id'];
		} else {
			$ids = [intval($params['id'])];
		}
		
		unset($params['id']);
		$params['update_time'] = time();
		$flag = $this->goodsTypeMdl->where(['id'=>['in',$ids]])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['id'=>$ids];
	}

	
	/**
	 * 删除类型
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public  function del($params)
	{
		$validate = new Validate([
		    
		    'id' => 'require',
		],[
			'id.require'=>'ID必填',
			
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		   
		];
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		if($params['id'] && is_array($params['id'])) {
			$ids = $params['id'];
		}else {
			$ids = [$params['id']];
		}
		unset($params['id']);
		if($this->goodsMdl->field('id')->where(['type_id'=>['in',$ids]])->select()) {
			throw new HttpException(404,'该类型有商品使用不能删除');
		}
		$flag = $this->goodsTypeMdl->where(['id'=>['in',$ids]])->delete();
		
		
		if(!$flag) throw new HttpException(404,'删除失败');
		return ['id'=>$ids];
	}
	/**
	 * 根据类型ID获取规格值
	 * @return [type] [description]
	 */
	public function getSpecValue(array $params) {
		$validate = new Validate([
		    
		    'type_id' => 'require',
		],[
			'type_id.require'=>'ID必填',
			
		]);
		$check_data = [
		   
		    'type_id' => intval($params['type_id']),
		   
		];
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$type_data = $this->goodsTypeMdl->where(['id'=>intval($params['type_id'])])->find();
		if(!$type_data) {
			throw new HttpException(404,'暂无商品类型数据');
		}
		//根据规格id获取规格值
		$spec_value = $this->specMdl->where(['id'=>['in',explode(',', $type_data['spec_id'])]])->select();

		if(!$spec_value) throw new HttpException(404,'暂无规格数据');
		foreach ($spec_value as $key => &$value) {
			$value['spec_value'] = $this->specValueMdl->where(['spec_id'=>intval($value['id'])])->select();
		}

		return ['data'=>$spec_value];
	}

	
}

?>