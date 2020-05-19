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
namespace api\v1\web\cart;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Goods;
use \model\Product;
use \model\Cart;
/**
 * 
 */
class carts
{



    
	public function __construct()
	{
		$this->goodsMdl = model('Goods');
        $this->productMdl = model('Product');
        $this->cartMdl = model('Cart');
        
		
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
    public function save(array $params) {
    	
        

    	$validate = new Validate([
            
            'accessToken' => 'require',
            'goods_id'=>'require|number|egt:1',
            'product_id'=>'require|number|egt:1',
            'num'=>'require|number|egt:1',
        ],[
            'accessToken.require'=>'accessToken必填',
            'goods_id.require'=>'商品ID必填',
            'goods_id.number'=>'商品ID必须是整数',
            'goods_id.egt'=>'商品ID必填大于等于1',
            'product_id.require'=>'sku ID必填',
            'product_id.number'=>'sku ID必须是整数',
            'product_id.egt'=>'sku ID必填大于等于1',
            'num.require'=>'sku ID必填',
            'num.number'=>'sku ID必须是整数',
            'num.egt'=>'sku ID必填大于等于1',
        ]);
        $checkData = [
            'accessToken'=>$params['accessToken'],
            'goods_id' => intval($params['goods_id']),
            'product_id'=>intval($params['product_id']),
            'num'=>intval($params['num']),
        ];

        if (!$validate->check($checkData)) {
            throw new HttpException(404,$validate->getError());
        }

        $member_id = userMake::check(trim($params['accessToken']));
        unset($params['accessToken']);
        if(!$member_id) {
            throw new HttpException(404,'解析用户ID错误！');
        }
        $where = ['member_id'=>$member_id,'goods_id'=>intval($params['goods_id']),'product_id'=>intval($params['product_id'])];
        $check_cart_data = $this->cartMdl->where($where)->find();
        
        if(!$check_cart_data) {
            //新增
            $cart_data = [
                'product_id'=>intval($params['product_id']),
                'goods_id'=>intval($params['goods_id']),
                'num'=>intval($params['num']),
                'member_id'=>$member_id,
            ];
           
            $flag = $this->cartMdl->save($cart_data);
        }else {
            $cart_data =[
                'num'=>intval($check_cart_data['num']) + intval($params['num']),
            ];
            $flag = $this->cartMdl->where($where)->update($cart_data);
        }
        if(!$flag) throw new HttpException(404,'加入购物车失败！');
        return ['data'=>$flag];

    }
    
   

	
}

?>