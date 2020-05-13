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
/**
 * 菜单
 */


return [

	'menulist'=>[
		'goods_rule'=>[
			'label'=>'商品管理',
			'icon'=>'&#xe722;',//图标
			'href'=>'',
			'rule'=>'goods_rule',
			'children'=>[

				['label'=>'商品列表','rule'=>'goods.list','parent'=>'goods_rule','href'=>'page/goods/goods-list.html'],
				['label'=>'商品分类','rule'=>'goods.cate','parent'=>'goods_rule','href'=>'page/goods/goods-cate.html'],
				['label'=>'商品类型','rule'=>'goods.type','parent'=>'goods_rule','href'=>'page/goods/goods-type.html'],
				['label'=>'商品规格','rule'=>'goods.spec','parent'=>'goods_rule','href'=>'page/goods/goods-spec.html'],
				['label'=>'商品品牌','rule'=>'goods.brand','parent'=>'goods_rule','href'=>'page/goods/brand.html'],
			],
		],
		'order_rule'=>[
			'label'=>'订单管理',
			'icon'=>'&#xe6a2;',
			'href'=>'',
			'rule'=>'order_rule',
			'children'=>[
				['label'=>'订单列表','rule'=>'order.list','parent'=>'order_rule','href'=>'page/order/order-list.html'],
				
			],
		],
		'member_rule'=>[
			'label'=>'会员管理',
			'icon'=>'&#xe6b8;',
			'href'=>'',
			'rule'=>'member_rule',
			'children'=>[
				['label'=>'会员列表','rule'=>'member.list','parent'=>'member_rule','href'=>'page/member/member-list.html'],
			],
		],

		'admin_rule'=>[
			'label'=>'管理员管理',
			'icon'=>'&#xe70b;',
			'href'=>'',
			'rule'=>'admin_rule',
			'children'=>[
				['label'=>'管理员列表','rule'=>'user.rule','parent'=>'admin_rule','href'=>'page/admin/user.html'],
				['label'=>'角色管理','rule'=>'role.rule','parent'=>'admin_rule','href'=>'page/admin/role.html']
			],
		],

		'image_rule'=>[
			'label'=>'首页管理',
			'icon'=>'&#xe6d7;',
			'href'=>'',
			'rule'=>'image_rule',
			'children'=>[
				['label'=>'轮播图管理','rule'=>'banner.rule','parent'=>'image_rule','href'=>'page/image/banner.html'],
				['label'=>'挂件管理','rule'=>'widget.rule','parent'=>'image_rule','href'=>'page/widget/widget.html'],
				
			],
		],
	],
];
?>