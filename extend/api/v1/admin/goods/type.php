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
namespace api\v1\admin\goods;
use \think\exception\HttpException;
use \think\helper;
use \think\Db;
use \think\Validate;
use \think\Cache;
use api\v1\admin\userMake;
use \think\Config;
use \model\GoodsType;
/**
 * 
 */
class type
{




	public function __construct()
	{
		$this->goodsTypeMdl = model('GoodsType');
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
    		['field'=>'type_name','title'=>'类型名称','width'=>'80','sort'=>false,'fixed'=>''],
    		
    	];

    	return $return;
    }


    public function get(array $params) {
    	$limit = isset($params['limit']) ? intval($params['limit']) : config('paginate')['list_rows'];
		$offset = isset($params['page']) ?  (intval($params['page'])-1)*$limit:1;
		unset($params['limit'],$params['page']);
		$typeList['count'] = $this->goodsTypeMdl->where(array_filter($params))->count();
		
		$typeList['data'] = $this->goodsTypeMdl->where(array_filter($params))->limit(''.$offset.','.$limit.'')->select();
		
		
		return $typeList;
    }
    /**
     * 保存类型
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function save(array $params)
    {
    	
    	$validate = new Validate([
		    
		    'type_name' => 'require',
		    'spec_id'=>'require',
		],[
			'type_name.require'=>'类型名称必填',
			'spec_id.require'=>'规格必填',
		]);

		$check_data = [
		   
		    'type_name' => $params['type_name'],
		    'spec_id' => $params['spec_id'],
		];

		
		
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		$data = [
			'type_name'=>trim($params['type_name']),
			'spec_id'=>is_array($params['spec_id']) ? implode(',', $params['spec_id']) : $params['spec_id'],
			'disabled'=>intval($params['disabled']),
			'create_time'=>time(),
			'update_time'=>time(),
		];
		
		if(isset($params['id']) && $params['id']) {
			$flag = $this->goodsTypeMdl->where(['id'=>intval($params['id'])])->update($data);
		}else {
			$flag = $this->goodsTypeMdl->save($data);
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
    public function getRow(array $params) {
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
		$rs = $this->goodsTypeMdl->where(['id'=>$id])->find();
		
		if(!$rs) throw new HttpException(404,'获取失败');
		$data['data'] = $rs;
		$data['data']['spec_id'] = $rs['spec_id'] ? explode(',', $rs['spec_id']) : [];
		
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
		$flag = $this->goodsTypeMdl->where(['id'=>$id])->update($params);
		
		if(!$flag) throw new HttpException(404,'修改失败！');
		return ['data'=>$flag];
	}

	
	/**
	 * 删除类型
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
			
		]);
		$check_data = [
		   
		    'id' => intval($params['id']),
		   
		];
		if (!$validate->check($check_data)) {
		    throw new HttpException(404,$validate->getError());
		}

		
		$flag = $this->goodsTypeMdl->where(['id'=>intval($params['id'])])->delete();
		
		
		if(!$flag) throw new HttpException(404,'删除失败');
		return ['id'=>intval($params['id'])];
	}

	
}

?>