<?php
namespace app\index\controller;
use \think\Rpc;
use \think\Request;
use \think\ThinkApi;
use \think\exception\HttpException;
use \think\Config;
class Api extends ThinkApi
{
    public function __construct()
	{
		$this->request = Request::instance();
	}
    public function index()
    {
       
        $request = $this->request->request();
        
        $apis = Config::load(CONF_PATH . DS . 'api' . EXT)['routes'][$request['version']][$request['source']]; //config('routes');
       
        if (!array_key_exists($request['method'], $apis))
        {
        	
            $this->error("Api [".$request['method']."] not defined");
        }else {

            //验证签名
            
            $method = $request['method'];
            $source = $request['source'];
            $version = $request['version'];
            $sign  = $request['sign'];
            unset($request['s'],$request['method'],$request['source'],$request['version'],$request['sign']);
            /*if(trim($request['sign']) != $this->sign($request)) {
                $this->error('sign error',[],400);
            }*/
            try {

                $response = Rpc::call($method,$source,$version,$request);
                $this->success('success',$response ,0);
                
                
            } catch (HttpException $e) {
                $this->error($e->getMessage(),[],400);
            }
            
            
            
        }
    }
}
