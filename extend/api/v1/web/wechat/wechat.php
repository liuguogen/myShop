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
namespace api\v1\web\wechat;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;


/**
 * 
 */
class wechat
{




	public function __construct()
	{
		
		
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
    public function get(array $params) {
    	
        $validate = new Validate([
            
            'code' => 'require'
        ],[
            'code.require'=>'ID必填'
        ]);
        $checkData = [
           
            'code' => intval($params['code']),
        ];

        if (!$validate->check($checkData)) {
            throw new HttpException(404,$validate->getError());
        }
        $url  ='https://api.weixin.qq.com/sns/jscode2session?appid='.config('wechat')['appid'].'&secret='.config('wechat')['appsecret'].'&js_code='.trim($params['code']).'&grant_type=authorization_code';
    	$data = file_get_contents($url);

        $response_data = json_decode($data,1);
        return ['data'=>$response_data];

    }
    

	
}

?>