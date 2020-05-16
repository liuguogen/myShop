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
namespace api\v1\admin\specification;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use \think\Config;
use \model\Specs;
use \model\SpecValues;
# use \think\cache\driver\Redis;
/**
 * 
 */
class spec
{




	public function __construct()
	{

		$this->specMdl = model('specs');

		$this->specValuesMdl = model('SpecValues');
		
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


    

    /**
     * 保存规格
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {
    	$validate = new Validate([
		    
		    'spec_name' => 'require',
		    'spec_values'=>'require',
		],[
			'spec_name.require'=>'规格名称必填',
			'spec_values.require'=>'规格值必填',
		]);
		$checkData = [
		   
		    'spec_name' => $params['spec_name'],
		    'spec_values' => $params['spec_values'],
		];

		if (!$validate->check($checkData)) {
		    throw new HttpException(404,$validate->getError());
		}

		Db::startTrans();
		
		try {

			$spec_value_data = $params['spec_values'];

			unset($params['spec_values']);
			if(isset($params['id']) && $params['id']) {
			 	//update
				 $params['update_time'] = time();
				 $this->specMdl->where(['id'=>intval($params['id'])])->update($params);
				

				 foreach ($spec_value_data as $key => &$value) {

				 	if(isset($value['spec_id']) && $value['spec_id']) {
				 		$spec_id =intval($value['spec_id']);
				 		unset($value['spec_id']);
				 		$value['spec_id'] = intval($params['id']);
				 		$this->specValuesMdl->where(['id'=>$spec_id])->update($value);
				 	}else {
				 		$value['spec_id'] = intval($params['id']);
				 		$this->specValuesMdl->insert($value);

				 	}
				 	

				 	
				 	
				 }

				 
			}else {
				
				//insert
				$params['create_time'] = time();
				$params['update_time'] = time();
				$this->specMdl->insert($params);
				$last_id = Db::table('spec')->getLastInsID();
				foreach ($spec_value_data as $key => &$value) {
					$value['spec_id'] = $last_id;
				}

				foreach ($spec_value_data as $k => $v) {
					$this->specValuesMdl->insert($v);
				}
				
			}


			Db::commit();

			return ['spec_id'=>isset($last_id) ? $last_id : intval($params['id'])];
		} catch (\Exception $e) {
			Db::rollback();
			throw new HttpException(404,$e->getMessage());
		}
		
		
		
		
		
    }
	
	/**
	 * 规格获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get(array $params) {
		
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		unset($params['limit'],$params['page']);
		$specList['count'] = $this->specMdl->where(array_filter($params))->count();
		
		$specList['data'] = $this->specMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		// foreach ($specList['data'] as $key => &$value) {
		// 	$value['item'] = $this->specMdl->spec_values()->where(['spec_id'=>$value['id']])->select();
		// }
		
		
		return $specList;
	}

	/**
	 * 更新主数据
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
		$checkData = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($checkData)) {
		    throw new HttpException(404,$validate->getError());
		}
		$id = intval($params['id']);
		unset($params['id']);
		
		$flag = $this->specMdl->where(['id'=>$id])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['data'=>$flag];
	}

	/**
	 * 获取单条数据
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function getRow(array $params)
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

		$id = intval($params['id']);
		unset($params['id']);
		$data['data'] = $this->specMdl->where(['id'=>$id])->find();
		$data['data']['item'] = $this->specValuesMdl->where(['spec_id'=>$id])->select();
		return $data;
	}
	/**
	 * 删除规格
	 * @param  array
	 * @return [type]
	 */
	public function del(array $params)
	{
		$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$checkData = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($checkData)) {
		    throw new HttpException(404,$validate->getError());
		}

		Db::startTrans();

		try {
			
			$this->specMdl->where(['id'=>intval($params['id'])])->delete();
			$this->specValuesMdl->where(['spec_id'=>intval($params['id'])])->delete();
			Db::commit();
			return ['id'=>intval($params['id'])];
		} catch (\Exception $e) {
			Db::rollback();
			throw new HttpException(404,$e->getMessage());
		}

		
	}

	/**
	 * 删除规格值
	 * @return [type] [description]
	 */
	public function delValue(array $params) {
		$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$checkData = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($checkData)) {
		    throw new HttpException(404,$validate->getError());
		}

		if($params['id'] && is_array($params['id'])) {
			$ids = $params['id'];
		} else {
			$ids = [intval($params['id'])];
		}
		unset($params['id']);
		$flag = $this->specValuesMdl->where(['id'=>['in',$ids]])->delete();
		if(!$flag) throw new HttpException(404,'删除失败');
		return ['id'=>$ids];
	}

	
}

?>