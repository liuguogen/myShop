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
use \model\GoodsType;

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
        $this->goodsTypeMdl = model('GoodsType');
		
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
                $sku_value[] = explode(',', $value['spec_value']);
                $spec_goods[] = array(
                    $value['spec_text'] => $value['id']
                );
            }
            
            $type_spec = $this->goodsTypeMdl->field('spec_id')->where(['id'=>$goods_data['type_id']])->find();
            if($type_spec) {
                
                $sku_list = $this->specMdl->field('id,spec_name')->where(['id'=>['in',explode(',', $type_spec['spec_id'])]])->select();

                if($sku_list) {
                    $sku_lists = collection($sku_list)->toArray();
                    foreach ($sku_lists as $k => $v) {
                        
                        $_specValue = $this->specValueMdl->field('id,spec_value')->where(['spec_id'=>intval($v['id'])])->select();
                        $_specValue = collection($_specValue)->toArray();
                        
                        foreach ($_specValue as $s_k => $s_v) {
                            foreach ($sku_value as $kk => $vv) {
                                
                                if(in_array($s_v['id'], $vv)) {
                                   
                                    
                                    $sku_lists[$k]['spec_value'][] = $_specValue[$s_k];
                                }
                            }
                        }

                        
                    }
                    
                    foreach ($sku_lists as $key => &$value) {

                       if(isset($value['spec_value']) && $value['spec_value']) {
                            $value['spec_value'] = $this->array_unique_bykey($value['spec_value'],'spec_value');
                       }else {
                            unset($sku_lists[$key]);
                       }
                    }
                    
                    

                    $goods_data['sku_list'] = $sku_lists;
                }
               
            }
            
            $goods_data['spec_goods'] = $spec_goods;
            $goods_data['product'] =  $products_data;
        }
       
        return ['data'=>$goods_data];

    }
    /**
     * 二维数组去重
     * @param  [type] $arr [description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    private function array_unique_bykey($arr, $key){
        $tmp_arr = array();
        foreach($arr as $k => $v)
        {
            if(in_array($v[$key], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            {
                unset($arr[$k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值
            }else {
                $tmp_arr[$k] = $v[$key];  //将不同的值放在该数组中保存
            }
       }
       //ksort($arr); //ksort函数对数组进行排序(保留原键值key)  sort为不保留key值
      return $arr;
   }
   /**
    * 获取所有商品
    * @return [type] [description]
    */
   public function getAll( $params = []) {


        $limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
        $offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
        unset($params['limit'],$params['page']);
        $where = [
            'sales_status'=>1,
        ];

        if(isset($params['name']) && $params['name']) {
            $where['name'] =['like','%'.$params['name'].'%'];
        }
        
        $goodsList['count'] = $this->goodsMdl->where($where)->count();
        $goodsList['data'] = $this->goodsMdl->where($where)->limit(''.$offset.','.$limit.'')->select();

       return ['data'=>$goodsList];
   }

	
}

?>