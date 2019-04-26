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
namespace api\v1\admin\brands;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Brands;
use \think\cache\driver\Redis;
/**
 * 
 */
class brand
{




	public function __construct()
	{
		$this->brandMdl = model('brands');
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


    private function getField() {
    	 $return['field'] =   [
    		['field'=>'id','title'=>'编号','width'=>'80','sort'=>true,'fixed'=>'left'],
    		['field'=>'brand_name','title'=>'品牌名称','width'=>'80','sort'=>false,'fixed'=>''],
    		['field'=>'brand_url','title'=>'品牌地址','width'=>'80','sort'=>false,'fixed'=>''],
    	];

    	return $return;
    }

    /**
     * 保存品牌
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save($params)
    {
    	$validate = new Validate([
		    
		    'brand_name' => 'require'
		],[
			'brand_name.require'=>'品牌名称必填'
		]);
		$check_data = [
		   
		    'brand_name' => $params['brand_name'],
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$data = [
			'brand_name'=>trim($params['brand_name']),
			'brand_logo'=>trim($params['brand_logo']),
			'brand_url'=>trim($params['brand_url']),
			'brand_keywords'=>serialize(explode('|',trim($params['brand_keywords']))),
			'brand_desc'=>trim($params['brand_desc']),
			'disabled'=>intval($params['disabled']),
			'create_time'=>time(),
		];

		$flag = $this->brandMdl->save($data);
		if(!$flag) {
			throw new HttpException(404,'保存失败！');
		}
		return ['data'=>$flag];
		
    }
	
	/**
	 * 品牌获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get($params) {


		
		/*$validate = new Validate([
		    
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
		}*/

		$brandMdl = model('brands');
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:0;
		$brandList['count'] = $brandMdl->count();
		$brandList['data'] = $brandMdl->limit(''.$offset.','.$limit.'')->select();
		return $brandList;
	}

	
}

?>