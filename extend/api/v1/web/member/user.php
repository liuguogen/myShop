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
use \model\Address;

/**
 * 
 */
class user
{




	public function __construct()
	{
		$this->memberMdl = model('Member');
        $this->addressMdl = model('Address');
		
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

            //查询是否存在
            $check_user   = $this->memberMdl->where(['openid'=>$response_data['openid']])->find();


            if($check_user) {
                $accessToken = userMake::make($member_id,['openid'=>$check_user['openid'],'create_time'=>$check_user['create_time'],'update_time'=>$check_user['update_time']]);
                return ['data'=>['openid'=>$check_user['openid'],'accessToken'=>$accessToken]];
            }
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
    /**
     * 用户地址保存
     * @return [type] [description]
     */
    public function saveAddr(array $params) {
        $validate = new Validate([
            
            'accessToken' => 'require',
            'name'=>'require',
            'mobile'=>'require',
            'province'=>'require',
            'city'=>'require',
            'area'=>'require',
            'address'=>'require',
        ],[
            'accessToken.require'=>'accessToken必填',
            'name.require'=>'姓名必填',
            'mobile.require'=>'手机号必填',
            'province.require'=>'省份必填',
            'city.require'=>'市必填',
            'area.require'=>'区必填',
            'address.require'=>'详细地址必填',
        ]);
        $data = [
           
            'accessToken' => $params['accessToken'],
            'name'=>$params['name'],
            'mobile'=>$params['mobile'],
            'province'=>$params['province'],
            'city'=>$params['city'],
            'area'=>$params['area'],
            'address'=>$params['address'],
        ];

        if (!$validate->check($data)) {
            throw new HttpException(404,$validate->getError());
        }
        $member_id = userMake::check(trim($params['accessToken']));
        unset($params['accessToken']);
        if(!$member_id) {
            throw new HttpException(404,'解析用户ID错误！');
        }

        if(isset($params['id']) && $params['id']) {
            $id = $params['id'];
            unset($params['id']);
            //更新数据
            $params['update_time'] = time();
            $flag = $this->addressMdl->where(['member_id'=>intval($member_id),'id'=>intval($id)])->update($params);
        }else {
            //创建数据
            $params['member_id'] = $member_id;
            $params['create_time'] = time();
            $params['update_time'] = time();
            $flag = $this->addressMdl->save($params);
        }

        if(!$flag) throw new HttpException(404,'地址保存错误！');
        return  ['data'=>isset($params['id']) && $params['id'] ? $params['id']  : $flag];
    }

    /**
     * 获取地址列表
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function getAddr(array $params) {
        $validate = new Validate([
            
            'accessToken' => 'require',
            
        ],[
            'accessToken.require'=>'accessToken必填',
            
        ]);
        $data = [
           
            'accessToken' => $params['accessToken'],
            
        ];

        if (!$validate->check($data)) {
            throw new HttpException(404,$validate->getError());
        }

        $member_id = userMake::check(trim($params['accessToken']));
        unset($params['accessToken']);

        $addressData = $this->addressMdl->where(['member_id'=>intval($member_id)])->select();

        return ['data'=>$addressData ? $addressData : []];


    }
    /**
     * 获取单条地址
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function getAddrRow(array $params) {
        $validate = new Validate([
            
            'accessToken' => 'require',
            'id'=>'require',
            
        ],[
            'accessToken.require'=>'accessToken必填',
            'id.require'=>'地址ID必填',
            
        ]);
        $data = [
           
            'accessToken' => $params['accessToken'],
            'id'=>$params['id'],
            
        ];

        if (!$validate->check($data)) {
            throw new HttpException(404,$validate->getError());
        }

        $member_id = userMake::check(trim($params['accessToken']));
        unset($params['accessToken']);

        $addressData = $this->addressMdl->where(['id'=>intval($params['id']),'member_id'=>intval($member_id)])->find();
        return ['data'=>$addressData ? $addressData :[]];
    }
    

	
}

?>