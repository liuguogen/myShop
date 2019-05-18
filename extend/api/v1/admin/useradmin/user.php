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
use \model\Admin;
/**
 * 
 */
class user
{


	/**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        
        $return =  [
        	['field'=>'username','type'=>'string','valid'=>'require|max:25','desc'=>'用户名','example'=>''],
        	['field'=>'password','type'=>'string','valid'=>'require','desc'=>'密码','example'=>''],
        	
        ];
        return $return;
    }
	/**
	 * 用户登录
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function login(array $params)
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
		
		
		$adminData = model('admin')->field('id,username,password,avatar,is_disabled')->where('username',trim($params['username']))->find();

		if(!$adminData) {
			throw new HttpException(404,'没有该用户!');
		}

		$adminData = $adminData->toArray();
		
		if($adminData['is_disabled']==0) {
			throw new HttpException(404,'该账号没有启用请联系管理员!');
		}
		if(!$PasswordHashs->CheckPassword($params['password'],$adminData['password'])) {
				throw new HttpException(404,'密码错误!');
		}
		
		
		//Db::execute('update admin set last_login_ip =:last_login_ip,last_login_at =:last_login_at where id =:id',['last_login_ip'=>$_SERVER['REMOTE_ADDR'],'last_login_at'=>time(),'id'=>$adminData['id']]);
		//
		model('admin')->update(['last_login_ip'=>$_SERVER['REMOTE_ADDR'],'update_time'=>time()],['id'=>$adminData['id']]);
		$accessToken = userMake::make($adminData['id'],$adminData);
		
		cookie('accessToken',$accessToken,time()+3600*24*7);
		cookie('adminName',$adminData['username'],time()+3600*24*7);

		return ['data'=>['accessToken'=>$accessToken,'username'=>$adminData['username'],'avatar'=>$adminData['avatar']]];
			
		
		
	
	}

	/**
	 * 获取用户
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function get(array $params)
	{
		$adminMdl = model('admin');
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		unset($params['limit'],$params['page']);
		
		
		$adminList['count'] = $adminMdl->where('is_super',0)->where(array_filter($params))->count();
		$adminList['data'] = $adminMdl->where('is_super',0)->where(array_filter($params))->order('update_time desc')->limit(''.$offset.','.$limit.'')->select();

		if(!$adminList['data']) {
			throw new HttpException(404,'暂无数据！');
		}
		return $adminList;
	}
	

	public function save(array $params) {

		if(isset($params['file'])) {
			unset($params['file']);
		}
		$validate = new Validate([
		    'username'  => 'require',
		    'role_id'=>'require|number',
		    'password' => 'require',
		    'password_confrim'=>'require',

		],[
			'username.require'=>'登录名必填',
			'role_id.require'=>'角色必填',
			'role_id.number'=>'角色必须为数字',
			'password.require'=>'密码必填',
			'password_confrim.require'=>'确认密码必填',
		]);
		
		$data = [
		    'username'  =>isset($params['username']) ?  $params['username'] :'',
		    'role_id'=>isset($params['role_id']) ? $params['role_id'] :'',
		    'password' =>isset($params['password']) ? $params['password'] :'',
		    'password_confrim' =>isset($params['password_confrim']) ? $params['password_confrim'] :'',
		   
		];
		
		if (!$validate->check($data)) {
			
		    throw new HttpException(404,$validate->getError());
		}
		$PasswordHashs = new \think\PasswordHash(8, false);  
		
		$userData = [
			'username'=>$params['username'],
			'password'=>$PasswordHashs->HashPassword($params['password']),
			'avatar'=>isset($params['avatar']) ? $params['avatar'] :'',
			'role_id'=>intval($params['role_id']),
			'is_super'=>0,//非超管
			'status'=>1,
			'create_time'=>time(), 
		];

		//校验用户是否已经使用
		$check_user = model('admin')->field('id')->where('username',$params['username'])->find();
		
		if($check_user) {

			throw new HttpException(404,'该登录名已被使用请换一个！');
		}
		$flag = model('admin')->save($userData);
		if(!$flag) {
			throw new HttpException(404,'保存失败！');
		}
		return ['data'=>$flag];
	}

	
}

?>