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
use \model\Product;
use \model\GoodsCate;
use \model\GoodsType;
use \model\Brands;
/**
 * 
 */
class home
{




	public function __construct()
	{
		$this->goodsMdl = model('Goods');
		$this->productMdl = model('Product');
		$this->goodsTypeMdl = model('GoodsType');
		$this->brandMel = model('Brands');
		$this->goodsCate = model('GoodsCate'); 
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
    		['field'=>'name','title'=>'商品名称','width'=>'80','sort'=>false,'fixed'=>''],
    		
    	];

    	return $return;
    }

    /**
     * 获取首页数据
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function get(array $params) {
    	
    	$goodsList = $this->goodsMdl->where(['sales_status'=>1])->select();
    	foreach ($goodsList as $key => &$value) {
    		$cate_name = $this->goodsCate->field('cate_name')->where(['id'=>intval($value['cate_id'])])->find();
			$value['cate_name']  = $cate_name ? $cate_name['cate_name'] : '';
			$brand_name = $this->brandMel->field('brand_name')->where(['id'=>intval($value['brand_id'])])->find();
			$value['brand_name'] = $brand_name ? $brand_name['brand_name'] :'';
			$type_name = $this->goodsTypeMdl->field('type_name')->where(['id'=>intval($value['type_id'])])->find();
			$value['type_name'] = $type_name ? $type_name['type_name'] :  '';
			//$value['product'] = $this->productMdl->where(['goods_id'=>$value['id']])->select();
    	}
    	
    	$return['goods'] = [
    		'goods'=>$goodsList,
    		'count'=>count($goodsList),
    	];
    	
		return ['data'=>$return];
    }
    

	
}

?>