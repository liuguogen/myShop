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

return  [

	//接口路由配置
    'routes'=>[


        'v1'=>[
            //后台接口数据
            'admin'=>[

                'menu.get'=>['uses'=>'\menu\menulist@get','desc'=>'获取菜单列表'],
                'admin.login'=>['uses'=>'\useradmin\user@login','desc'=>'后台用户登录'],
                'admin.user.get'=>['uses'=>'\useradmin\user@gets','desc'=>'获取后台用户'],
                'admin.user.getrow'=>['uses'=>'\useradmin\user@getRow','desc'=>'获取后台用户单条数据'],
                'admin.user.save'=>['uses'=>'\useradmin\user@save','desc'=>'保存后台用户'],
                'admin.user.del'=>['uses'=>'\useradmin\user@delUser','desc'=>'删除用户'],
                'admin.loginout'=>['uses'=>'\useradmin\loginout@loginout','desc'=>'后台用户退出'],
                'brand.get'=>['uses'=>'\brands\brand@get','desc'=>'获取品牌列表'],
                'brand.update'=>['uses'=>'\brands\brand@update','desc'=>'更新品牌数据'],
                'brand.getrow'=>['uses'=>'\brands\brand@getRow','desc'=>'获取品牌单条数据'],
                'brand.save'=>['uses'=>'\brands\brand@save','desc'=>'保存品牌'],
                'brand.del'=>['uses'=>'\brands\brand@delBrand','desc'=>'删除品牌'],
                'role.save'=>['uses'=>'\roles\role@save','desc'=>'保存角色'],
                'role.get'=>['uses'=>'\roles\role@gets','desc'=>'获取角色列表'],
                'role.getrow'=>['uses'=>'\roles\role@getRow','desc'=>'获取角色单条数据'],
                'image.upload.signle'=>['uses'=>'\images\image@singleUpload','desc'=>'单个图片上传'],
                'image.upload.multiple'=>['uses'=>'\images\image@multipleUpload','desc'=>'多个图片上传'],
                'spec.get'=>['uses'=>'\specification\spec@get','desc'=>'规格获取'],
                'spec.save'=>['uses'=>'\specification\spec@save','desc'=>'规格保存'],
                'spec.update'=>['uses'=>'\specification\spec@update','desc'=>'更新规格数据'],
                'spec.getrow'=>['uses'=>'\specification\spec@getRow','desc'=>'获取规格单条数据'],
                'spec.del'=>['uses'=>'\specification\spec@del','desc'=>'删除规格'],
                'spec.val.del'=>['uses'=>'\specification\spec@delValue','desc'=>'删除规格值'],
                'cate.save'=>['uses'=>'\classification\cate@save','desc'=>'保存商品分类'],
                'cate.get'=>['uses'=>'\classification\cate@get','desc'=>'获取商品分类'],
                'cate.del'=>['uses'=>'\classification\cate@del','desc'=>'删除商品分类'],
                'goods.save'=>['uses'=>'\goods\products@save','desc'=>'商品保存'],
                'goods.get'=>['uses'=>'\goods\products@get','desc'=>'商品获取'],
                'goods.del'=>['uses'=>'\goods\products@del','desc'=>'商品删除'],
                'goods.getrow'=>['uses'=>'\goods\products@getRow','desc'=>'商品获取单条数据'],
                'goods.update'=>['uses'=>'\goods\products@update','desc'=>'商品更新'],
                'goods.type.save'=>['uses'=>'\goods\type@save','desc'=>'商品类型保存'],
                'goods.type.get'=>['uses'=>'\goods\type@get','desc'=>'商品类型获取'],
                'goods.type.del'=>['uses'=>'\goods\type@del','desc'=>'商品类型删除'],
                'goods.type.spec.get'=>['uses'=>'\goods\type@getSpecValue','desc'=>'根据商品类型获取规格值'],
                'goods.type.update'=>['uses'=>'\goods\type@update','desc'=>'商品类型更新数据'],
                'goods.type.getrow'=>['uses'=>'\goods\type@getRow','desc'=>'商品类型获取单条数据'],
                'banner.get'=>['uses'=>'\images\banner@get','desc'=>'轮播图获取'],
                'banner.save'=>['uses'=>'\images\banner@save','desc'=>'轮播图保存'],
                'banner.update'=>['uses'=>'\images\banner@update','desc'=>'轮播图更新'],
                'banner.getrow'=>['uses'=>'\images\banner@getRow','desc'=>'轮播图取单条数据'],
                'widget.get'=>['uses'=>'\widget\widgets@get','desc'=>'首页挂件获取'],
                'widget.save'=>['uses'=>'\widget\widgets@save','desc'=>'首页挂件保存'],
                'widget.getrow'=>['uses'=>'\widget\widgets@getRow','desc'=>'首页挂件取单条数据'],
                'widget.del'=>['uses'=>'\widget\widgets@del','desc'=>'首页挂件删除'],
                'widget.update'=>['uses'=>'\widget\widgets@update','desc'=>'首页挂件更新'],
                'member.get'=>['uses'=>'\member\members@get','desc'=>'获取用户列表'],
                'order.get'=>['uses'=>'\order\trade@get','desc'=>'获取订单列表'],
            ],
            //前端接口数据
            'web'=>[
                'home.get'=>['uses'=>'\home\home@get','desc'=>'获取首页数据'],
                'member.save'=>['uses'=>'\member\user@save','desc'=>'会员创建'],
                'wechat.info'=>['uses'=>'\wechat\wechat@get','desc'=>'微信用户信息获取'],
                'goods.get'=>['uses'=>'\goods\products@get','desc'=>'商品详情获取'],
                'goods.query'=>['uses'=>'\goods\products@getAll','desc'=>'获取全部商品'],
                'address.save'=>['uses'=>'\member\user@saveAddr','desc'=>'用户地址保存'],
                'address.get'=>['uses'=>'\member\user@getAddr','desc'=>'用户地址获取'],
                'address.getrow'=>['uses'=>'\member\user@getAddrRow','desc'=>'获取单个用户地址'],
                'order.create'=>['uses'=>'\order\trade@createOrder','desc'=>'订单创建'],
                'order.get'=>['uses'=>'\order\trade@get','desc'=>'订单获取'],
                'cart.save'=>['uses'=>'\cart\carts@save','desc'=>'保存到购物车'],
                'cart.get'=>['uses'=>'\cart\carts@get','desc'=>'获取购物车'],
                'cart.num'=>['uses'=>'\cart\carts@getCartNum','desc'=>'获取购物车数量'],
            ],
        ],

        'v2'=>[],
        
    ],
];
?>