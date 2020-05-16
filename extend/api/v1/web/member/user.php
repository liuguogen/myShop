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
namespace api\v1\web\member;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Member;

/**
 * 
 */
class user
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
        	//['field'=>'accessToken','type'=>'string','valid'=>'require','desc'=>'accessToken','example'=>''],
        	['field'=>'page','type'=>'number','valid'=>'','desc'=>'页码','example'=>'1'],
        	['field'=>'limit','type'=>'number','valid'=>'','desc'=>'偏移量','example'=>'10'],
        	
        ];
        return $return;
    }

    /**
     * 创建用户
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function save(array $params) {
    	


    	$validate = new Validate([
            
            'openid' => 'require'
        ],[
            'openid.require'=>'ID必填'
        ]);
        $checkData = [
           
            'openid' => intval($params['openid']),
        ];

        if (!$validate->check($checkData)) {
            throw new HttpException(404,$validate->getError());
        }

        $data = [
            'openid'=>trim($params['openid']),
        ];
        if(isset($params['accessToken']) && $params['accessToken']) {

        }else {

            if($this->memberMdl->insert($data)) {
                $member_id = $this->memberMdl->getLastInsID();
                $accessToken = userMake::make($member_id,$data);
            }else {
                throw new HttpException(404,'用户注册失败');
            }
            
        }
        return ['data'=>$accessToken];

    }
    

	
}

?>