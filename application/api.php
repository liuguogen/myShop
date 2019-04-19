<?php

return  [

	//接口路由配置
    'routes'=>[

        'menu.get'=>['uses'=>'\menu\menulist@get','text'=>'获取菜单接口列表','version'=>'v1','source'=>'admin'],

        'admin.login'=>['uses'=>'\useradmin\user@login','text'=>'后台用户登录','version'=>'v1','source'=>'admin'],
        'admin.loginout'=>['uses'=>'\useradmin\loginout@loginout','text'=>'后台用户退出','version'=>'v1','source'=>'admin'],
    ],
];
?>