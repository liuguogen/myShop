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
namespace api\v1\admin\useradmin;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use \api\v1\admin\userMake;
/**
 * 
 */
class loginout
{


	/**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        
        $return =  [
        	['field'=>'accessToken','type'=>'string','valid'=>'require','desc'=>'accessToken','example'=>''],
        	
        ];
        return $return;
    }
	
	/**
	 * 退出登录
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function loginout(array $params) {
		
		$validate = new Validate([
		    
		    'accessToken' => 'require'
		]);
		$data = [
		   
		    'accessToken' => $params['accessToken'],
		];

		if (!$validate->check($data)) {
		    throw new HttpException(404,$validate->getError());
		}
		$id = userMake::check(trim($params['accessToken']));
		if(!$id) {
			throw new HttpException(404,'解析用户ID错误！');
		}
		$adminData = db('admin')->field('id,username')->where('id',intval($id))->find();
		if(!$adminData) {
			throw new HttpException(404,'没有该用户'.$adminData['username']);
		}
		cookie('adminId',null);
		cookie('adminName',null);
		return ['data'=>'succ'];
	}

	public function saveAdmin(array $params) {


		if(!isset($params['username'])) {

			throw new HttpException(404,'账号名不能为空!');
		}

		if(isset($params['file'])) {
			unset($params['file']);
		}
		if(!isset($params['password'])) {

			throw new HttpException(404,'密码不能为空!');
		}
		if(!isset($params['group_id'])) {

			throw new HttpException(404,'用户组不能为空!');
		}
		if(!isset($params['department_id'])) {

			throw new HttpException(404,'部门不能为空!');
		}
		if(!isset($params['full_name'])) {

			throw new HttpException(404,'姓名不能为空!');
		}
		$params['password'] = md5($params['password']);
		$params['is_super'] = 0;//非超管

		if($params['group_id']==2) {
			if($params['group_type'] =='ordinary') {
				$params['is_disabled'] = 0;//禁止登陆
				$params['rule'] = json_encode(array('rule'=>[]));
			}else {
				$params['is_disabled'] = 1;
				$params['is_dispatch'] = 1;
				$params['rule'] = json_encode(array('rule'=>array('admin.article','admin.articlesource.list')));
			}
		}else {
			$params['rule'] = json_encode(array('rule'=>[]));
		}
		$op_id = intval($params['op_id']);
		unset($params['op_id']);
		$params['status'] = 1;//正常
		if(isset($params['admin_id']) && $params['admin_id']) {

			$admin_id = intval($params['admin_id']);
			unset($params['admin_id']);
			
			$flag = Db::table('admin')->where('admin_id',$admin_id)->update($params);
			logger::log($op_id,'管理员','edit',$admin_id);
		}else {
			$params['created_at'] = time();
			$admin_id = intval($params['admin_id']);
			unset($params['admin_id']);
			//新增
			$flag = Db::table('admin')->insert($params);
			logger::log($op_id,'管理员','add',$flag);
		}


		if(!$flag){
			throw new HttpException(404,'保存失败!');
		}
			
		return ['data'=>$flag];
	}

	public function getAdmin($params)
	{
		
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:0;
		if(isset($params['username']) && $params['username']) {
			$count = Db::table('admin')->where('username','like','%'.trim($params['username']).'%')->count();
		}else {
			$count = Db::table('admin')->count();
		}
		

		if(isset($params['admin_id']) && $params['admin_id']) {
			
			$data = Db::table('admin')->where('admin_id',intval($params['admin_id']))->find();
			
		}elseif (isset($params['username']) && $params['username']) {
			$data = Db::table('admin')->where('username','like','%'.trim($params['username']).'%')->limit(''.$offset.','.$limit.'')->select(); 
			foreach ($data as $key => &$value) {
				$department_name = Db::table('department')->where('department_id',$value['department_id'])->value('department_name');
				
				$group_name = Db::table('user_group')->where('group_id',$value['group_id'])->value('group_name');
				
				$value['department_name'] = $department_name ?:'';
				$value['group_name'] = $group_name ?:'';


			}
		}else {
			$data = Db::table('admin')->order('admin_id desc')->limit(''.$offset.','.$limit.'')->select(); 

			foreach ($data as $key => &$value) {
				$department_name = Db::table('department')->where('department_id',$value['department_id'])->value('department_name');
				
				$group_name = Db::table('user_group')->where('group_id',$value['group_id'])->value('group_name');
				
				$value['department_name'] = $department_name ?:'';
				$value['group_name'] = $group_name ?:'';


			}
		}
		
		if(!$data) {
			throw new HttpException(404,'暂无数据!');
		}
		return ['data'=>$data,'count'=>$count?:0];
	}

	public function delAdmin($params)
	{
		if(!isset($params['admin_id'])) {

			throw new HttpException(404,'admin_id不能为空!');
		}

		$flag = Db::table('admin')->where('admin_id',intval($params['admin_id']))->delete();
		if(!$flag){
			throw new HttpException(404,'删除失败!');
		}
		return ['data'=>$flag];
	}
}

?>