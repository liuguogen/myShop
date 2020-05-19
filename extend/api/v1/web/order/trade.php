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
namespace api\v1\web\order;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Goods;
use \model\Product;
use \model\Order;
use \model\OrderItem;
use \model\OrderSales;
/**
 * 
 */
class trade
{



	public function __construct()
	{
		$this->goodsMdl = model('Goods');
		$this->productMdl = model('Product');
		$this->orderMdl = model('Order');
		$this->orderItemMdl = model('OrderItem');
		$this->orderSalesMdl = model('OrderSales'); 
	
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
     * 订单创建
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function createOrder(array $params) {
    	
        $validate = new Validate([
            
            'accessToken' => 'require',
            'goods_id'=>'require|number|egt:1',
            'product_id'=>'require|number|egt:1',
            'num'=>'require|number|egt:1',
            'name'=>'require',
            'mobile'=>'require',
            'province'=>'require',
            'city'=>'require',
            'area'=>'require',
            'address'=>'require',
        ],[
            'accessToken.require'=>'accessToken必填',
            'goods_id.require'=>'商品ID必填',
            'goods_id.number'=>'商品ID必须是数字',
            'goods_id.egt'=>'商品ID必须大于等于1',
            'product_id.require'=>'sku ID必填',
            'product_id.number'=>'sku ID 必须是数字',
            'product_id.egt'=>'sku ID必须大于等于1',
            'num.require'=>'购买数量必填',
            'num.number'=>'购买数量必须是数字',
            'num.egt'=>'购买数量必须大于等于1',
            'name.require'=>'姓名必填',
            'mobile.require'=>'手机号必填',
            'province.require'=>'省份必填',
            'city.require'=>'市必填',
            'area.require'=>'区必填',
            'address.require'=>'详细地址必填',
        ]);
        $data = [
           
            'accessToken' => $params['accessToken'],
            'goods_id'=>$params['goods_id'],
            'product_id'=>$params['product_id'],
            'num'=>$params['num'],
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
        $goods_data = $this->goodsMdl->where(['id'=>intval($params['goods_id'])])->find();
        if(!$goods_data) {
            throw new HttpException(404,'商品好像不见了~');
        }
        //获取商品价格
        $product_data = $this->productMdl->where(['goods_id'=>intval($params['goods_id']),'id'=>intval($params['product_id'])])->find();
        if(!$product_data) {
            throw new HttpException(404,'商品sku好像不见了~');
        }
        //开启事务
        Db::startTrans();
        try {
            $order_no = $this->_generate_order_no();
            
            $order_data = [
                'order_no'=>$order_no,
                'member_id'=>$member_id,
                'amount'=>$product_data['price'] * $params['num'],//价格乘以商品数量
                'name'=>trim($params['name']),//购买人姓名
                'mobile'=>trim($params['mobile']),//购买人手机号码
                'province'=>trim($params['province']),//省
                'city'=>trim($params['city']),//市
                'area'=>trim($params['area']),//区
                'address'=>trim($params['address']),//详细地址
                'memo'=>isset($params['memo']) ? $params['memo'] : '',
                'create_time'=>time(),
                'update_time'=>time(),
            ];
            $order_item_data = [
                'goods_id'=>intval($params['goods_id']),
                'product_id'=>intval($params['product_id']),
                'name'=>$goods_data['name'],
                'price'=>$goods_data['price'],
                'num'=>intval($params['num']),
                'create_time'=>time(),
                'update_time'=>time(),
            ];


            $order_sales_data = [
                'order_no'=>$order_no,
                'goods_id'=>intval($params['goods_id']),
                'product_id'=>intval($params['product_id']),
                'sales_num'=>intval($params['num']),
                'create_time'=>time(),
                'update_time'=>time(),
            ];

            if($goods_data['store_type']==1) { //下单减库存
                
                $order_sales_data['free_num'] = intval($params['num']);
            }
            //todo 付款减库存
            $this->orderMdl->insert($order_data);
            $order_id = $this->orderMdl->getLastInsID();
            $order_item_data['order_id'] = $order_id;
            $this->orderItemMdl->insert($order_item_data);
            $this->orderSalesMdl->insert($order_sales_data);
            Db::commit();
            return ['data'=>['order_no'=>$order_no]];
        } catch (\Exception $e) {
            Db::rollback();
            throw new HttpException(404,$e->getMessage());
        }
        
    }
    /**
     * 订单号生成
     * @return [type] [description]
     */
    private function _generate_order_no() {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    
	
}

?>