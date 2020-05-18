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
namespace api\v1\admin\images;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use \think\Config;
use \OSS\OssClient;
use \OSS\Core\OssException;
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
	public function singleUploads($params=null) {
		
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
	public function singleUpload($params=null) {

		//如果配置阿里云oss
		if(!empty(config('aliOss'))) {
			$ossClient = new OssClient(
		        config('aliOss')['KeyId'], 
		        config('aliOss')['KeySecret'], 
		        config('aliOss')['Endpoint']
        	);
        	#执行阿里云上传
	      	try{

	      		$result = [];
	      		foreach ($_FILES as $key => $value) {
	      			$ext = substr($value['name'], strrpos($value['name'], '.')+ 1);
					$filname = date('Ymd') . DS . md5(microtime(true)).'.'.$ext;
					$result = $ossClient->uploadFile(
				        config('aliOss')['Bucket'], 
				        $filname, 
				        $value['tmp_name']
			    	);
	      		}
	      		return ['data'=>isset($result['info']['url']) && $result['info']['url'] ? ['src'=>$result['info']['url']] : ''];
	      		
	      	}catch(\Exception $e){
	      		throw new HttpException(404,'请上传图片!');
	      	}
	      }else {
	      	//本地上传
	      	return $this->singleUploads();
	      }
      	
	}

	
}

?>