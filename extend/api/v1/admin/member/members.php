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
namespace api\v1\admin\member;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use \think\Config;
use \model\Member;
/**
 * 
 */
class members
{


	public function __construct()
	{
		$this->memberMdl = model('Member');
		
	}

	/**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        
        $return =  [
        	['field'=>'username','type'=>'string','valid'=>'require','desc'=>'username','example'=>''],
        	
        ];
        return $return;
    }
	
	/**
	 * 用户获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get(array $params) {


		
		
		
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:0;
		unset($params['limit'],$params['page']);
		$brandList['count'] = $this->memberMdl->where(array_filter($params))->count();
		$brandList['data'] = $this->memberMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		return $brandList;
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
		$rs = $this->memberMdl->where(['id'=>$id])->find();
		
		if(!$rs) throw new HttpException(404,'获取失败');
		
		
		
		return ['data'=>$rs];
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
			$ids = $params['id'];
		}else {
			$ids = [intval($params['id'])];
		}
		
		unset($params['id']);
		$params['update_time'] = time();
		$flag = $this->memberMdl->where(['id'=>['in',$ids]])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['id'=>$ids];
	}

	
}

?>