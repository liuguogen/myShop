<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------
// | 修改者: liuguogen (本权限类在原3.2.3的基础上修改过来的)
// +----------------------------------------------------------------------
namespace api\v1\admin\useradmin;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
/**
 * 
 */
class user
{
	/**
	 * 用户登录
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function login($params)
	{


		$validate = new Validate([
		    'username'  => 'require|max:25',
		    'password' => 'require'
		]);
		$data = [
		    'username'  => $params['username'],
		    'password' => $params['password'],
		];

		if (!$validate->check($data)) {
		    throw new HttpException(404,$validate->getError());
		}
		
		$PasswordHashs = new \think\PasswordHash(8, false);  
		//$hashedPassword = $PasswordHashs->HashPassword($password); 
		
		
		$adminData = db('admin')->field('id,username,password')->where('username',trim($params['username']))->find();
		if(!$adminData) {
			throw new HttpException(404,'没有该用户!');
		}
		
		if(!$PasswordHashs->CheckPassword($params['password'],$adminData['password'])) {
				throw new HttpException(404,'密码错误!');
		}
		
			
		Db::execute('update admin set last_login_ip =:last_login_ip,last_login_at =:last_login_at where id =:id',['last_login_ip'=>$_SERVER['REMOTE_ADDR'],'last_login_at'=>time(),'id'=>$adminData['id']]);
		$accessToken = userMake::make($adminData['id'],$adminData);

		cookie('accessToken',$accessToken,time()+3600*24*7);
		cookie('adminName',$adminData['username'],time()+3600*24*7);
		return ['accessToken'=>$accessToken,'username'=>$adminData['username']];
			
		
		
	
	}
	/**
	 * 退出登录
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function loginout($data) {
		
		if(!isset($data['admin_id'])) {

			throw new HttpException(404,'admin_id不能为空!');
		}
		$adminData = db('admin')->where('admin_id',intval($data['admin_id']))->find();
		if(!$adminData) {
			throw new HttpException(404,'没有该用户'.$adminData['username']);
		}
		cookie('adminId',null);
		cookie('adminName',null);
		return ['data'=>'succ'];
	}

	public function saveAdmin($params) {


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