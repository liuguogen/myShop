<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liuguogen <liuguogen_vip@163.com>
// +----------------------------------------------------------------------

namespace think;
use \think\exception\HttpResponseException;
class Rpc
{
    /**
     * 接口调用类
     * @param  [type] $method     [description]
     * @param  [type] $source     [description]
     * * @param  [type] $version     [description]
     * @param  array  $parameters [description]
     * @return [type]             [description]
     */
    public static function call($method,$source, $version ,$parameters = array())
    {
        
        $apis = Config::load(CONF_PATH . DS . 'api' . EXT)['routes'][$version][$source]; //config('routes');

        $version =  '\\'.$version; //版本

        $source = '\\'.$source; //来源
        
        if (array_key_exists($method, $apis))
        {
            list($class, $method) = explode('@', $apis[$method]['uses']);
            
        }
        else
        {
            
            throw new HttpResponseException("Api [$method] not defined");
        }
        
        $class = '\api'.$version.$source.$class;
        
        $instance  =  new $class();
        
        return call_user_func(array($instance, $method), $parameters);
        

        
    }
}
