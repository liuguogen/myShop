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
use \think\Config;
use \model\GoodsCate;
/**
 * 
 */
class cate
{




	public function __construct()
	{
		$this->goodsCateMdl = model('goodsCate');
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
		if(isset($params['path'])) {
			$data['path'] = intval($params['path']);
		}
		if(isset($params['id']) && $params['id']) {
			$flag = $this->goodsCateMdl->where(['id'=>intval($params['id'])])->update($data);
		}else {
			$flag = $this->goodsCateMdl->save($data);
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

	        if ($v['pid'] == $parentId){
	            unset($data[$k]);
	            if ($l < 2){
	            //小于三级
	                $v['childer'] = $this->_treeNode($data,$v['id'],$l+1);
	            }

	            $list[] = $v;

	        }
	    }
	    return $list;


	    
	} 
	/**
	 * 分类获取
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get(array $params) {

		if(isset($params['id']) && $params['id']) {
			$data = $this->goodsCateMdl->where(['id'=>intval($params['id'])])->find();
		}else {
			$data = $this->goodsCateMdl->select();
			
			$data = $this->_treeNode(collection($data)->toArray(),0);
		}
		
		return ['data'=>$data ? : []];
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
		if($params['id'] && is_array($params['id'])) {
			$ids = $params['id'];
		}else {
			$ids = [intval($params['id'])];
		}
		
		unset($params['id']);
		$params['update_time'] = time();
		$flag = $this->goodsCateMdl->where(['id'=>['in',$ids]])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['id'=>$ids];
	}

	
	/**
	 * 删除分类
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public  function del($params)
	{
		$validate = new Validate([
		    
		    'id' => 'require',
		    'pid'=>'require',
		],[
			'id.require'=>'ID必填',
			'pid.require'=>'pid必填'
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		    'pid'=>intval($params['pid'])
		];
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		if($params['pid'] ==0) {
			//查询二级分类
			$ids = $this->_delCate(intval($params['id']));
			$flag = $this->goodsCateMdl->where(['id'=>['in',$ids]])->delete();
		}else {
			$flag = $this->goodsCateMdl->where(['id'=>intval($params['id'])])->delete();
		}
		
		if(!$flag) throw new HttpException(404,'删除失败');
		return ['id'=>intval($params['id'])];
	}

	private function _delCate($id) {
		$two_cate_obj = $this->goodsCateMdl->where(['pid'=>$id])->select();
		$two_cate = collection($two_cate_obj)->toArray();
		$three_cate = [];
		$del_ids_arr = [];
		if($two_cate) {
			$three_cate = array_column($two_cate, 'id');
			$ids = $this->goodsCateMdl->where(['pid'=>['in',$three_cate]])->select();

			$del_ids = collection($ids)->toArray();
			if($del_ids) {
				$del_ids_arr =  array_column($del_ids, 'id');
			}
		}

		return array_merge([$id],$three_cate,$del_ids_arr);
	}
}

?>