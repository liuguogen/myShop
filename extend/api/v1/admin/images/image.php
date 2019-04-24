<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------
// | 修改者: liuguogen (本权限类在原3.2.3的基础上修改过来的)
// +----------------------------------------------------------------------
namespace api\v1\admin\images;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
/**
 * 
 */
class image
{


	/**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        
        $return =  [
        	['field'=>'accessToken','type'=>'string','valid'=>'require','desc'=>'accessToken','example'=>''],
        	
        ];
        return $return;
    }
	
	/**
	 * 单个图片上传
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function singleUpload($params=null) {
		
		$file = request()->file('file');
		 if($file){
		 	$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		 	
		 	if($info){

		 		$imageData  = ['src'=>config('imageUrl')['url'].'uploads/'.$info->getSaveName()];
		 		return ['data'=>$imageData];
		 	}else {
		 		throw new HttpException(404,$file->getError());
		 	}
		 }else {
		 	throw new HttpException(404,'请上传图片!');
		 }
	}

	
}

?>