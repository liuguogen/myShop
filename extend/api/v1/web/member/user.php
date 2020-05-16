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
            
            'code' => 'require'
        ],[
            'code.require'=>'code必填'
        ]);
        $checkData = [
           
            'code' => intval($params['code']),
        ];

        if (!$validate->check($checkData)) {
            throw new HttpException(404,$validate->getError());
        }

       

        $url  ='https://api.weixin.qq.com/sns/jscode2session?appid='.config('wechat')['appid'].'&secret='.config('wechat')['appsecret'].'&js_code='.trim($params['code']).'&grant_type=authorization_code';
        $response_data = file_get_contents($url);
        $response_data = json_decode($response_data,1);

        if(!isset($response_data['openid'])) {
            throw new HttpException(404,isset($response_data['errmsg']) ? $response_data['errmsg'] : '获取openid失败');
        }

         $data = [
            'openid'=>trim($response_data['openid']),
            'create_time'=>time(),
            'update_time'=>time(),
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
        $rs_data = [
            'accessToken'=>$accessToken,
        ];
        if(isset($response_data['openid']) && $response_data['openid']) {
            $rs_data['openid'] = $response_data['openid'];
        }
        return ['data'=>$rs_data];

    }
    

	
}

?>