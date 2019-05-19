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
    public function process()
    {
       
        $request = $this->request->request();
        $method = $request['method'];
        $source = $request['source'];
        $version = $request['version'];
        $sign  = $request['sign'];
        unset($request['s'],$request['method'],$request['source'],$request['version'],$request['sign']);
        try {

            $response = Rpc::call($method,$source,$version,$request);
            $this->success('success',$response ,0);
                
                
        } catch (HttpException $e) {
            $this->error($e->getMessage(),[],400);
        }
       
        
    }
}
