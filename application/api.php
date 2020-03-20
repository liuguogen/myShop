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
                'cate.save'=>['uses'=>'\classification\cate@save','desc'=>'保存商品分类'],
                'cate.get'=>['uses'=>'\classification\cate@get','desc'=>'获取商品分类'],
            ],
        ],

        'v2'=>[],
        
    ],
];
?>