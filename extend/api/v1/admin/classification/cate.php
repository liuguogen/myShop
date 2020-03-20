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
namespace api\v1\admin\classification;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\Classification;
/**
 * 
 */
class cate
{




	public function __construct()
	{
		$this->classificationMdl = model('classification');
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
    		['field'=>'cate_name','title'=>'分类名称','width'=>'80','sort'=>false,'fixed'=>''],
    		
    	];

    	return $return;
    }

    /**
     * 保存分类
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {
    	$validate = new Validate([
		    
		    'cate_name' => 'require'
		],[
			'cate_name.require'=>'分类名称必填'
		]);
		$check_data = [
		   
		    'cate_name' => $params['cate_name'],
		];

		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$data = [
			'cate_name'=>trim($params['cate_name']),
			'cate_img'=>trim($params['cate_img']),
			'disabled'=>intval($params['disabled']),
			'create_time'=>time(),
			'update_time'=>time(),
		];
		if(isset($params['pid'])) {
			$data['pid'] = intval($params['pid']);
		}
		if(isset($params['id']) && $params['id']) {
			$flag = $this->classificationMdl->where(['id'=>intval($params['id'])])->update($data);
		}else {
			$flag = $this->classificationMdl->save($data);
		}
		
		if(!$flag) {
			throw new HttpException(404,'保存失败！');
		}
		return ['data'=>$flag];
		
    }
	private function _treeNode($data,$parentId = 0,$l=0)
	{


		$list =array();

	    foreach ($data as $k=>$v){

	        if ($v['p_id'] == $parentId){
	            unset($data[$k]);
	            if ($l < 2){
	            //小于三级
	                $v['childer'] = $this->_treeNode($data,$v['cate_id'],$l+1);
	            }

	            $list[] = $v;

	        }
	    }
	    return $list;


	    
	} 
	/**
	 * 品牌获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get(array $params) {

		//$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		//$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		//unset($params['limit'],$params['page']);
		//$brandList['count'] = $this->brandMdl->where(array_filter($params))->count();
		$data = $this->classificationMdl->select();
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
		$flag = $this->brandMdl->where(['id'=>intval($params['id'])])->delete();
		if(!$flag) throw new HttpException(404,'删除失败');
		return ['id'=>intval($params['id'])];
	}
}

?>