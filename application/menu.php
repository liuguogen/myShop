<?php

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

				['label'=>'商品列表','rule'=>'goods.list','parent'=>'goods_rule','href'=>'page/goods/list.html'],
				['label'=>'商品分类','rule'=>'goods.cate','parent'=>'goods_rule','href'=>'page/goods/cate.html'],
				['label'=>'商品品牌','rule'=>'goods.brand','parent'=>'goods_rule','href'=>'page/goods/brand.html'],
			],
		],
		'member_rule'=>[
			'label'=>'会员管理',
			'icon'=>'&#xe6b8;',
			'href'=>'',
			'rule'=>'member_rule',
			'children'=>[
				['label'=>'会员列表','rule'=>'member.list','parent'=>'member_rule','href'=>'page/member/index.html'],
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
	],
];
?>