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
namespace app\library;
use \think\Config;
use \think\exception\HttpResponseException;

class Rpc
{
    /**
     * 接口调用类
     * @param  [string] $method     [方法]
     * @param  [string] $source     [来源]
     * @param  [string] $version    [版本号]
     * @param  array  $parameters   [参数]
     * @return [array]              [array]
     */
    public static function call($method,$source, $version ,$parameters = array())
    {
        
        $apis = Config::load(CONF_PATH . DS . 'api' . EXT)['routes'][$version][$source]; 
        $version =  '\\'.$version; //版本
        $source = '\\'.$source; //来源
        
        if (!array_key_exists($method, $apis)){
            throw new HttpResponseException("Api [$method] not defined");        
        }
        list($class, $method) = explode('@', $apis[$method]['uses']);
        $class = '\api'.$version.$source.$class;
        
        $instance  =  new $class();
        
        return call_user_func(array($instance, $method), $parameters);
        
        
    }
}