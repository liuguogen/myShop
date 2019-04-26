<?php

return  [

	//接口路由配置
    'routes'=>[

        'menu.get'=>['uses'=>'\menu\menulist@get','text'=>'获取菜单列表','version'=>'v1','source'=>'admin'],

        'admin.login'=>['uses'=>'\useradmin\user@login','text'=>'后台用户登录','version'=>'v1','source'=>'admin'],
        'admin.get'=>['uses'=>'\useradmin\user@get','text'=>'获取后台用户','version'=>'v1','source'=>'admin'],
        'admin.user.save'=>['uses'=>'\useradmin\user@save','text'=>'保存后台用户','version'=>'v1','source'=>'admin'],
        'admin.loginout'=>['uses'=>'\useradmin\loginout@loginout','text'=>'后台用户退出','version'=>'v1','source'=>'admin'],
        'brand.get'=>['uses'=>'\brands\brand@get','text'=>'获取品牌列表','version'=>'v1','source'=>'admin'],
        'brand.save'=>['uses'=>'\brands\brand@save','text'=>'保存品牌','version'=>'v1','source'=>'admin'],
        'role.save'=>['uses'=>'\roles\role@save','text'=>'保存角色','version'=>'v1','source'=>'admin'],
        'role.get'=>['uses'=>'\roles\role@get','text'=>'获取角色列表','version'=>'v1','source'=>'admin'],
        'image.upload.signle'=>['uses'=>'\images\image@singleUpload','text'=>'单个图片上传','version'=>'v1','source'=>'admin'],
        'image.upload.multiple'=>['uses'=>'\images\image@multipleUpload','text'=>'多个图片上传','version'=>'v1','source'=>'admin'],
    ],
];
?>