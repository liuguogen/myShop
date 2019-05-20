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

                'menu.get'=>['uses'=>'\menu\menulist@get','text'=>'获取菜单列表'],
                'admin.login'=>['uses'=>'\useradmin\user@login','text'=>'后台用户登录'],
                'admin.get'=>['uses'=>'\useradmin\user@get','text'=>'获取后台用户'],
                'admin.user.save'=>['uses'=>'\useradmin\user@save','text'=>'保存后台用户'],
                'admin.loginout'=>['uses'=>'\useradmin\loginout@loginout','text'=>'后台用户退出'],
                'brand.get'=>['uses'=>'\brands\brand@get','text'=>'获取品牌列表'],
                'brand.update'=>['uses'=>'\brands\brand@update','text'=>'更新品牌数据'],
                'brand.getrow'=>['uses'=>'\brands\brand@getRow','text'=>'获取品牌单条数据'],
                'brand.save'=>['uses'=>'\brands\brand@save','text'=>'保存品牌'],
                'role.save'=>['uses'=>'\roles\role@save','text'=>'保存角色'],
                'role.get'=>['uses'=>'\roles\role@get','text'=>'获取角色列表'],
                'image.upload.signle'=>['uses'=>'\images\image@singleUpload','text'=>'单个图片上传'],
                'image.upload.multiple'=>['uses'=>'\images\image@multipleUpload','text'=>'多个图片上传'],
                'spec.get'=>['uses'=>'\specification\spec@get','text'=>'规格获取'],
                'spec.save'=>['uses'=>'\specification\spec@save','text'=>'规格保存'],
                'spec.update'=>['uses'=>'\specification\spec@update','text'=>'更新规格数据'],
                'spec.getrow'=>['uses'=>'\specification\spec@getRow','text'=>'获取规格单条数据'],
                'spec.del'=>['uses'=>'\specification\spec@del','text'=>'删除规格'],
            ],
        ],

        'v2'=>[],
        
    ],
];
?>