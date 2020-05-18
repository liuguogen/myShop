<?php

namespace app\library;

use \think\exception\HttpResponseException;
use \think\exception\ValidateException;
use \think\Lang;
use \think\Loader;
use \think\Request;
use \think\Response;


/**
 * API控制器基类
 */
class ThinkApi
{

   

    /**
     * 构造方法
     * @access public
     * @param Request $request Request 对象
     */
   /* public function __construct(Request $request = null)
    {
        $this->request = is_null($request) ? Request::instance() : $request;

        // 控制器初始化
        $this->_initialize();

        // 前置操作方法
        if ($this->beforeActionList)
        {
            foreach ($this->beforeActionList as $method => $options)
            {
                is_numeric($method) ?
                                $this->beforeAction($options) :
                                $this->beforeAction($method, $options);
            }
        }
    }*/

    

   

    /**
     * 操作成功返回的数据
     * @param string $msg   提示信息
     * @param mixed $data   要返回的数据
     * @param string $type  输出类型
     * @param array $header 发送的 Header 信息
     */
    protected function success($msg = '', $data = '',$code=200, $type = 'json', array $header = [])
    {
        $this->result($data, $code, $msg, $type, $header);
    }

    /**
     * 操作失败返回的数据
     * @param string $msg   提示信息
     * @param mixed $data   要返回的数据
     * @param string $type  输出类型
     * @param array $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = '',$code=400, $type = 'json', array $header = [])
    {
    	
        $this->result($data, $code, $msg, $type, $header);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $data   要返回的数据
     * @param int    $code   返回的 code
     * @param mixed  $msg    提示信息
     * @param string $type   返回数据格式
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {

        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => date('Y-m-d H:i:s',Request::instance()->server('REQUEST_TIME')),
            'data' => isset($data['data']) ? $data['data'] :[],
            
        ];
        if(isset($data['count'])) {
            $result['count'] = $data['count'];
        }
        $type = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }


    public function sign(array $params) {
        unset($params['sign']);
        unset($params['s']);
        $token = config('appKey')['token'];        
        
        $sign = '';
        foreach ($params as $k => $v) {
            $sign.= $k.$v;
        }

        
        return md5($sign.$token);
    }

   

    
    
}
