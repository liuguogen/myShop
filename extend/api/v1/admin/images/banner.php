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
use api\v1\admin\userMake;
use \think\Config;
use \model\Banners;
use \model\Goods;
/**
 * 
 */
class banner
{


	public function __construct()
	{
		$this->bannerMdl = model('Banners');
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
        	['field'=>'banner_name','type'=>'string','valid'=>'require','desc'=>'accessToken','example'=>''],
        	
        ];
        return $return;
    }
	
	/**
	 * 轮播图获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get(array $params) {


		
		
		
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		unset($params['limit'],$params['page']);
		$brandList['count'] = $this->bannerMdl->where(array_filter($params))->count();
		$brandList['data'] = $this->bannerMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		return $brandList;
	}

	/**
     * 保存轮播图
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {


    	$validate = new Validate([
		    
		    'banner_name' => 'require',
		    'image'=>'require',
		],[
			'banner_name.require'=>'图片名称必填',
			'image.require'=>'图片必填'
		]);
		$check_data = [
		   
		    'banner_name' => $params['banner_name'],
		    'image'=>$params['image'],
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}
		if(isset($params['file'])) {
			unset($params['file']);
		}
		
		$data = [
			'banner_name'=>trim($params['banner_name']),
			'image'=>trim($params['image']),
			'goods_id'=>$params['goods_id'] ? $params['goods_id'] : 0,
			'disabled'=>intval($params['disabled']),
			'create_time'=>time(),
		];
		
		if(isset($params['id']) && $params['id']) {
			$data['update_time']  =time();
			$flag = $this->bannerMdl->where(['id'=>intval($params['id'])])->update($data);
		}else {
			$flag = $this->bannerMdl->save($data);
		}
		
		if(!$flag) {
			throw new HttpException(404,'保存失败！');
		}
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
		$rs = $this->bannerMdl->where(['id'=>$id])->find();
		
		if(!$rs) throw new HttpException(404,'获取失败');
		if($rs['goods_id']) {
			$rs['goods_name'] = $this->goodsMdl->field('name')->where(['id'=>$rs['goods_id']])->find()['name'];
		}
		$data['data'] = $rs;
		
		return $data;
	}

	
}

?>