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
namespace api\v1\web\goods;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Goods;
use \model\Product;
use \model\Specs;
use \model\SpecValues;

/**
 * 
 */
class products
{




	public function __construct()
	{
		$this->goodsMdl = model('Goods');
        $this->productMdl = model('Product');
        $this->specMdl = model('Specs');
        $this->specValueMdl = model('SpecValues');
		
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
     * 获取商品详情
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function get(array $params) {
    	


    	$validate = new Validate([
            
            'goods_id' => 'require'
        ],[
            'goods_id.require'=>'商品ID必填'
        ]);
        $checkData = [
           
            'goods_id' => intval($params['goods_id']),
        ];

        if (!$validate->check($checkData)) {
            throw new HttpException(404,$validate->getError());
        }

        $goods_data = $this->goodsMdl->where(['id'=>intval($params['goods_id'])])->find();

        if(!$goods_data) throw new HttpException(404,'商品好像不见了~');
        $goods_data['thumb'] = $goods_data['thumb'] ? explode(',', $goods_data['thumb']) :'';
        $goods_data['product']  = $this->productMdl->where(['goods_id'=>intval($params['goods_id'])])->select();

        if($goods_data['sku_type']=='many' && $goods_data['product']) {
            $products_data = collection($goods_data['product'])->toArray();
            foreach ($products_data as $key => $value) {
                 
                $spec_data = $this->specValueMdl->where(['id'=>['in',explode(',', $value['spec_value'])]])->select();

                if($spec_data) {
                    $_spec = [];
                    
                    foreach (collection($spec_data)->toArray() as $k => $v) {
                        $spec_name = $this->specMdl->where(['id'=>$v['spec_id']])->find()['spec_name'];
                       

                        $_spec[] =   [$spec_name=>$v['spec_value']];
                      
                    }
                }


                $products_data[$key]['spec_text'] =$_spec;
            }
            $goods_data['product'] = $products_data;
        }
       
        return ['data'=>$goods_data];

    }
    

	
}

?>