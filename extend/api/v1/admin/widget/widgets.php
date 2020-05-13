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
namespace api\v1\admin\widget;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Widget;
use \model\Goods;
/**
 * 
 */
class widgets
{


	public function __construct()
	{
		$this->widgetMdl = model('Widget');
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
        	['field'=>'title','type'=>'string','valid'=>'require','desc'=>'accessToken','example'=>''],
        	
        ];
        return $return;
    }
	
	/**
	 * 挂件获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get(array $params) {


		
		
		
		$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		unset($params['limit'],$params['page']);
		$brandList['count'] = $this->widgetMdl->where(array_filter($params))->count();
		$brandList['data'] = $this->widgetMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		return $brandList;
	}

	/**
     * 保存挂件
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {

    	$validate = new Validate([
		    
		    'title' => 'require',
		    'banner_img'=>'require',
		    'goods_id'=>'require',
		],[
			'title.require'=>'标题必填',
			'banner_img.require'=>'图片必填',
			'goods_id.require'=>'商品必选'
		]);
		$check_data = [
		   
		    'title' => $params['title'],
		    'banner_img'=>$params['banner_img'],
		    'goods_id'=>$params['goods_id'],
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}
		if(isset($params['file'])) {
			unset($params['file']);
		}
		
		$data = [
			'title'=>trim($params['title']),
			'title_desc'=>$params['title_desc'] ? $params['title_desc'] : '',
			'banner_img'=>trim($params['banner_img']),
			'sub_title'=>$params['sub_title'] ? $params['sub_title'] : '',
			'module_title'=>$params['module_title'] ? $params['module_title'] : '',
			'module_desc'=>$params['module_desc'] ? $params['module_desc'] : '',
			'goods_id'=>$params['goods_id'] ? $params['goods_id'] : '',
			'disabled'=>intval($params['disabled']),
			'create_time'=>time(),
		];
		
		if(isset($params['id']) && $params['id']) {
			$data['update_time']  =time();
			$flag = $this->widgetMdl->where(['id'=>intval($params['id'])])->update($data);
		}else {
			$flag = $this->widgetMdl->save($data);
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
		$rs = $this->widgetMdl->where(['id'=>$id])->find();
		
		if(!$rs) throw new HttpException(404,'获取失败');
		if($rs['goods_id']) {
			$goods_data = $this->goodsMdl->field('name')->where(['id'=>['in',explode(',', $rs['goods_id'])]])->select();
			$rs['goods_name'] = implode(',', array_column($goods_data, 'name'));
		}
		$data['data'] = $rs;
		
		return $data;
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
		$flag = $this->widgetMdl->where(['id'=>$id])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['data'=>$flag];
	}
	/**
	 * 删除挂件
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public  function del($params)
	{
		$validate = new Validate([
		    
		    'id' => 'require',
		],[
			'id.require'=>'ID必填',
			
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
		unset($params['id']);
		
		$flag = $this->widgetMdl->where(['id'=>['in',$ids]])->delete();
		
		
		if(!$flag) throw new HttpException(404,'删除失败');
		return ['id'=>$ids];
	}
}

?>