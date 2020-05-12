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
namespace api\v1\admin\brands;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Brands;
use \model\Goods;
/**
 * 
 */
class brand
{




	public function __construct()
	{
		$this->brandMdl = model('brands');
		$this->goodsMdl = model('Goods');
	}
	/**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        
        $return =  [
        	//['field'=>'accessToken','type'=>'string','valid'=>'require','desc'=>'accessToken','example'=>''],
        	['field'=>'page','type'=>'number','valid'=>'','desc'=>'页码','example'=>'1'],
        	['field'=>'limit','type'=>'number','valid'=>'','desc'=>'偏移量','example'=>'10'],
        	
        ];
        return $return;
    }


    private function getField() {
    	 $return['field'] =   [
    		['field'=>'id','title'=>'编号','width'=>'80','sort'=>true,'fixed'=>'left'],
    		['field'=>'brand_name','title'=>'品牌名称','width'=>'80','sort'=>false,'fixed'=>''],
    		['field'=>'brand_url','title'=>'品牌地址','width'=>'80','sort'=>false,'fixed'=>''],
    	];

    	return $return;
    }

    /**
     * 保存品牌
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {
    	$validate = new Validate([
		    
		    'brand_name' => 'require'
		],[
			'brand_name.require'=>'品牌名称必填'
		]);
		$check_data = [
		   
		    'brand_name' => $params['brand_name'],
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$data = [
			'brand_name'=>trim($params['brand_name']),
			'brand_logo'=>trim($params['brand_logo']),
			'brand_url'=>trim($params['brand_url']),
			'brand_keywords'=>serialize(explode('|',trim($params['brand_keywords']))),
			'brand_desc'=>trim($params['brand_desc']),
			'disabled'=>intval($params['disabled']),
			'create_time'=>time(),
		];
		if(isset($params['id']) && $params['id']) {
			$data['update_time'] = time();
			$flag = $this->brandMdl->where(['id'=>intval($params['id'])])->update($data);
		}else {
			$flag = $this->brandMdl->save($data);
		}
		
		if(!$flag) {
			throw new HttpException(404,'保存失败！');
		}
		return ['data'=>$flag];
		
    }
	
	/**
	 * 品牌获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get(array $params) {


		
		/*$validate = new Validate([
		    
		    'accessToken' => 'require'
		]);
		$data = [
		   
		    'accessToken' => $params['accessToken'],
		];

		if (!$validate->check($data)) {
		    throw new HttpException(404,$validate->getError());
		}
		$id = userMake::check(trim($params['accessToken']));
		if(!$id) {
			throw new HttpException(404,'解析用户ID错误！');
		}*/

		
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		unset($params['limit'],$params['page']);
		$brandList['count'] = $this->brandMdl->where(array_filter($params))->count();
		$brandList['data'] = $this->brandMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		return $brandList;
	}

	/**
	 * 更新数据
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function update(array $params)
	{


		$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}
		$id = intval($params['id']);
		unset($params['id']);
		$params['update_time'] = time();
		$flag = $this->brandMdl->where(['id'=>$id])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['data'=>$flag];
	}

	/**
	 * 获取单条数据
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function getRow(array $params)
	{
		$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$id = intval($params['id']);
		unset($params['id']);
		$rs = $this->brandMdl->where(['id'=>$id])->find();
		
		if(!$rs) throw new HttpException(404,'获取失败');
		$data['data'] = $rs;
		$data['data']['brand_keywords'] = $data['data']['brand_keywords']? implode('|',unserialize($data['data']['brand_keywords'])) : '';
		return $data;
	}
	/**
	 * 删除品牌
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public  function delBrand($params)
	{
		$validate = new Validate([
		    
		    'id' => 'require'
		],[
			'id.require'=>'ID必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		];
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		if($params['id'] && is_array($params['id'])) {
			$ids = $params['id'];
		}else {
			$ids = [$params['id']];
		}
		//查询是否有商品再用
		if($this->goodsMdl->field('id')->where(['brand_id'=>['in',$ids]])->select()){
			throw new HttpException(404,'该品牌有商品使用不能删除');
		}
		unset($params['id']);
		$flag = $this->brandMdl->where(['id'=>['in',$ids]])->delete();
		if(!$flag) throw new HttpException(404,'删除失败');
		return ['id'=>$ids];
	}
}

?>