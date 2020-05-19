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
namespace api\v1\web\home;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Goods;
use \model\Banners;
use \model\Widget;
/**
 * 
 */
class home
{



    static $expire_time = 86400;//过期时间
	public function __construct()
	{
		$this->goodsMdl = model('Goods');
		$this->bannerMdl = model('Banners'); 
		$this->widgetMdl = model('Widget'); 
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
     * 获取首页数据
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function get(array $params) {
    	

        //首页数据加缓存
        //
        $cacah_home_data = Cache::get('home_data');

        if($cacah_home_data) {
            return ['data'=>json_decode($cacah_home_data,1)];
        }
    	$widgetList = $this->widgetMdl->where(['disabled'=>1])->select();
    	$return = [];
    	if($widgetList) {
    		foreach ($widgetList as $key => &$value) {
	    		
				$value['product'] = $this->goodsMdl->where(['id'=>['in',explode(',', $value['goods_id'])]])->select();
	    	}

	    	$return['modules'] = $widgetList;
    	}
    	
    	
    	//获取轮播图
    	$bannerList = $this->bannerMdl->field('id,banner_name,image,goods_id,create_time,update_time')->where(['disabled'=>1])->select();
    	if($bannerList) {
    		
    		$return['banner'] = $bannerList;
    	}
    	//设置缓存
        Cache::set('home_data',json_encode($return),self::$expire_time);
		return ['data'=>$return];
    }
    

	
}

?>