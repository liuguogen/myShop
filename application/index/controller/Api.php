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
        
        $apis = Config::load(CONF_PATH . DS . 'api' . EXT)['routes']; //config('routes');

        if (!array_key_exists($request['method'], $apis))
        {
        	
            $this->error("Api [".$request['method']."] not defined");
        }else {
            try {
                if(isset($request['data']) && $request['data']) {
                    $response = Rpc::call($request['method'],$request['source'],json_decode($request['data'],true));
                }else {
                    $response = Rpc::call($request['method'],$request['source'],[]);
                }
                
                $this->success('success',$response ,200);
                
                
            } catch (HttpException $e) {
                $this->error($e->getMessage(),[],400);
            }
            
            
            
        }
    }
}
