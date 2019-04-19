<?php

/**
 * 菜单
 */


return [

	'menulist'=>[
		'goods.rule'=>[
			'label'=>'商品管理',
			'icon'=>'',//图标
			'children'=>[
				['label'=>'商品分类','rule'=>'goods.cate','parent'=>'goods.rule'],
				['label'=>'商品品牌','rule'=>'goods.brand','parent'=>'goods.rule'],
			],
		],
	],
];
?>