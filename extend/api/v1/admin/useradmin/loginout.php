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
		return ['accessToken'=>$params['accessToken']];
	}

	
}

?>