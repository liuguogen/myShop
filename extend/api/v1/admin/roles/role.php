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
namespace api\v1\admin\roles;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use \api\v1\admin\userMake;
use \think\Config;
use \model\Roles;
/**
 * 
 */
class role
{


	/**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        
        $return =  [
        	['field'=>'role_name','type'=>'string','valid'=>'require|max:25','desc'=>'角色名称','example'=>''],
 
        	
        ];
        return $return;
    }
	/**
	 * 保存角色
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function save(array $params)
	{

		$menuList = Config::load(CONF_PATH . DS . 'menu' . EXT)['menulist'];
		$role_name = $params['role_name'];
		$role_desc = $params['role_desc'];
		unset($params['role_name'],$params['role_desc']);

		foreach ($params['member_rule']['children'] as $key => $value) {

			
				$menu_data[] = $value;
			
		}


		foreach ($menuList as $key => &$value) {
			foreach ($value['children'] as $ck => $cv) {

				if(!in_array($cv['rule'], $menu_data)) {
					unset($menuList[$key]);
					unset($value['children'][$ck]);
				}
				
			}
		}
		
		
		$validate = new Validate([
		    'role_name'  => 'require',
		   
		]);
		$data = [
		    'role_name'  => $role_name,
		    
		];

		if (!$validate->check($data)) {
		    throw new HttpException(404,$validate->getError());
		}
		
		$saveData = [
			'role_name'=>$role_name,
			'menu_data'=>serialize($menuList),
			'role_params'=>serialize($params),
			'role_desc'=>$role_desc,
			'create_time'=>time(),
		];
			
		$flag  = model('roles')->save($saveData);
		return ['data'=>$flag];
		
	
	}

	/**
	 * 角色获取
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function gets(array $params) {
		$roleMdl = model('roles');
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:0;
		$brandList['count'] = $roleMdl->count();
		$brandList['data'] = $roleMdl->limit(''.$offset.','.$limit.'')->select();
		return $brandList;
	}
	

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

		$data = model('roles')->field('id,role_name,role_params,role_desc')->where(['id'=>intval($params['id'])])->find();
		
		if(!$data) throw new HttpException(404,'获取失败');
		$data['role_params'] = unserialize($data['role_params']);
		return ['data'=>$data] ;
	}

	
}

?>