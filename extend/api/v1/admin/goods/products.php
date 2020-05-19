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
namespace api\v1\admin\goods;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use \think\Config;
use \model\Goods;
use \model\Product;
use \model\GoodsCate;
use \model\GoodsType;
use \model\Brands;
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
		$this->goodsTypeMdl = model('GoodsType');
		$this->brandMel = model('Brands');
		$this->goodsCate = model('GoodsCate'); 
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


    private function getField() {
    	 $return['field'] =   [
    		['field'=>'id','title'=>'编号','width'=>'80','sort'=>true,'fixed'=>'left'],
    		['field'=>'name','title'=>'商品名称','width'=>'80','sort'=>false,'fixed'=>''],
    		
    	];

    	return $return;
    }


    public function get(array $params) {
    	
    	$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:0;
		unset($params['limit'],$params['page']);
		$goodsList['count'] = $this->goodsMdl->where(array_filter($params))->count();
		
		$goodsList['data'] = $this->goodsMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		if($goodsList['data']) {
			foreach ($goodsList['data'] as $key => &$value) {
				$cate_name = $this->goodsCate->field('cate_name')->where(['id'=>intval($value['cate_id'])])->find();
				$value['cate_name']  = $cate_name ? $cate_name['cate_name'] : '';
				$brand_name = $this->brandMel->field('brand_name')->where(['id'=>intval($value['brand_id'])])->find();
				$value['brand_name'] = $brand_name ? $brand_name['brand_name'] :'';
				$type_name = $this->goodsTypeMdl->field('type_name')->where(['id'=>intval($value['type_id'])])->find();
				$value['type_name'] = $type_name ? $type_name['type_name'] :  '';
			}
		}
		
		return $goodsList;
    }
    /**
     * 保存类型
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {
    	

    	$validate = new Validate([
		    
		    
		    'cate_id'=>'require',
		    'brand_id'=>'require',
		    'type_id'=>'require',
		    'name' => 'require',
		    'bn'=>'require',
		    'price'=>'require',
		    'mkt_price'=>'require',
		    'store'=>'require',
		    'goods_img'=>'require',
		],[
			'cate_id.require'=>'分类必填',
			'brand_id.require'=>'品牌必填',
			'type_id.require'=>'类型必填',
			'name.require'=>'商品名称必填',
			'bn.require'=>'商品货号必填',
			'price.require'=>'商品价格必填',
			'mkt_price.require'=>'参考价必填',
			'store.require'=>'库存必填',
			'goods_img.require'=>'商品主图必填'
		]);

		$check_data = [
		   
		    'cate_id' => $params['cate_id'],
		    'brand_id' => $params['brand_id'],
		    'type_id'=>$params['type_id'],
		    'name'=>$params['name'],
		    'bn'=>$params['bn'],
		    'price'=>$params['price'],
		    'mkt_price'=>$params['mkt_price'],
		    'store'=>$params['store'],
		    'goods_img'=>$params['goods_img']
		];

		
		
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		if(isset($params['file'])) {
			unset($params['file']);
		}

		
		Db::startTrans();
		try{
			$goods_data = [
				'name'=>trim($params['name']),//商品名称
				'cate_id'=>intval($params['cate_id']),//分类id
				'type_id'=>intval($params['type_id']),//类型id
				'brand_id'=>intval($params['brand_id']),//品牌id
				'sku_type'=>$params['sku_type'] ? trim($params['sku_type']) : 'signle',//sku 类型 many 多规格 signle 单规格
				'bn'=>trim($params['bn']),//商品货号
				'price'=>trim($params['price']),//商品价格
				'mkt_price'=>trim($params['mkt_price']),//商品参考价
				'weight'=>trim($params['weight']),//商品重量
				'store'=>intval($params['store']),//商品库存
				'sales_status'=>intval($params['sales_status']),//销售状态
				'store_type'=>intval($params['store_type']),//库存扣减方式
				'goods_img'=>trim($params['goods_img']),//商品主图
				'thumb'=> (!isset($params['thumb']) ) ? trim($params['goods_img']) : implode(',', $params['thumb']), //商品缩略图
				'goods_desc'=>$params['goods_desc'],//商品介绍
				'update_time'=>time(),

			];


			if(isset($params['sku_type']) && $params['sku_type']=='many') {

				if(!isset($params['product'])) {
					throw new HttpException(404,'缺少sku商品');
				}
				//多规格
				$validate = new Validate([
		    
		    
				    'product'=>'require',
				    
				],[
					'product.require'=>'sku商品必填'
				]);

				$check_data = [
				   
				    'product' => $params['product'],
				    
				];

				
				
				if (!$validate->check($check_data)) {
				    throw new HttpException(404,$validate->getError());
				}
				$product = [];
				foreach ($params['product'] as $key => $value) {
					foreach ($value as $k => $v) {

						
						$product[array_keys($v)[0]][$k] = array_values($v)[0];
					}
				}
				$product_data = [];
				foreach ($product as $key => $value) {
					$spec_value = $this->specValueMdl->field('spec_value')->where(['id'=>['in',explode(',', $key)]])->select();

					$product_data[] = [
						'name'=>$value['name'],
						'bn'=>$value['bn'],
						'price'=>$value['price'],
						'mkt_price'=>$value['mkt_price'],
						'store'=>$value['store'],
						'spec_value'=>$key,
						'spec_text'=>$spec_value ? implode(',', array_column($spec_value, 'spec_value')) : '',
						'update_time'=>time(),
					];
				}
				
				
			}else {
				//单规格
				
				$product_data = [[
					'name'=>trim($params['sku_type']) == 'many' ? $params['product']['name'] : trim($params['name']),
					'bn'=>trim($params['sku_type']) == 'many' ? $params['product']['bn'] : trim($params['bn']),
					'price'=>trim($params['sku_type']) == 'many' ? $params['product']['price'] : trim($params['price']),
					'mkt_price'=>trim($params['sku_type']) == 'many' ? $params['product']['mkt_price'] : trim($params['mkt_price']),
					'weight'=>trim($params['sku_type']) == 'many' ? $params['product']['weight'] : trim($params['weight']),
					'store'=>trim($params['sku_type']) == 'many' ? $params['product']['store'] : trim($params['store']),
					'sales_status'=>trim($params['sku_type']) == 'many' ? $params['product']['sales_status'] : trim($params['sales_status']),
					'update_time'=>time(),
				]];
			}
			

			if($params['id']) {
				//编辑
				
				$this->goodsMdl->where(['id'=>intval($params['id'])])->update($goods_data);
				if(isset($params['sku_type']) && $params['sku_type']=='many') {
					//多规格
				}else {


					$this->productMdl->where(['goods_id'=>intval($params['id'])])->update($product_data[0]);
				}
				
			}else {
				//新增
				$goods_data['create_time']  = time();
				$this->goodsMdl->insert($goods_data);
				$goods_id = $this->goodsMdl->getLastInsID();
				foreach ($product_data as $key => &$value) {
					$value['goods_id']= $goods_id;
					$value['create_time']  = time();
				}

				$this->productMdl->saveAll($product_data);
				

			}
			Db::commit();
			return ['data'=>isset($goods_id) && $goods_id ? $goods_id : intval($params['id'])];

		}catch(\Exception $e) {
			Db::rollback();
			throw new HttpException(404,$e->getMessage());
		}
		
		
    }

    /**
     * 获取单条数据
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function getRow(array $params) {
    	$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$id = intval($params['id']);
		unset($params['id']);
		$rs = $this->goodsMdl->where(['id'=>$id])->find();
		
		if(!$rs) throw new HttpException(404,'获取失败');
		$data['data'] = $rs;
		
		
		return $data;
    }
	
	/**
	 * 更新数据
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function update(array $params)
	{


		$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}
		$id = intval($params['id']);
		unset($params['id']);
		$params['update_time'] = time();
		$flag = $this->goodsMdl->where(['id'=>$id])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['data'=>$flag];
	}

	
	/**
	 * 删除商品
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public  function del($params)
	{

		$validate = new Validate([
		    
		    'id' => 'require',
		    
		],[
			'id.require'=>'ID必填',
			
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		   
		];
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}
		if(is_array($params['id']) && $params['id']) {
			$ids = $params['id'];
		}else {
			$ids = [$params['id']];
		}
		unset($params['id']);
		DB::startTrans();
		try{
			$this->goodsMdl->where(['id'=>['in',$ids]])->delete();
			$this->productMdl->where(['goods_id'=>['in',$ids]])->delete();
			Db::commit();
			return ['data'=>$ids];
		}catch(\Exception $e) {
			Db::rollback();
			throw new HttpException(404,$e->getMessage());
		}
		
		
		
		
	}

	
}

?>