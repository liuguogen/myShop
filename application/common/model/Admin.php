<?php

namespace app\common\model;
use \think\Model;
use \think\exception\HttpException;
use \api\v1\admin\userMake;
use \think\Config;
use \model\Roles;
class Admin extends Model {

	protected $pk = 'id';
	protected $table = 'admin';
	protected $autoWriteTimestamp = true;

	/**
	 * 判断是否是超级用户
	 * @param  [type]  $accessToken [description]
	 * @return boolean              [description]
	 */
	public function is_super($id) {
		
		if(!$id)  throw new HttpException(404,'缺少用户id！');
		

		$is_super = $this->field('id')->where('is_super',1)->where('id',$id)->find();
		
		return $is_super ? true :false;

	}
	/**
	 * 获取用户信息
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getUser($id)
	{
		if(!$id)  throw new HttpException(404,'缺少用户id！');
		$userData = $this->where('id',$id)->find()->toArray();
		
		return $userData ?  :false;
	}
	/**
	 * 获取菜单
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public  function getMenu($id)
	{
		
		if($this->is_super($id)) {
			$menuList = $this->__getMenu();
		}else {
			$userData = $this->getUser($id);
			$menuData = model('roles')->field('menu_data')->where('id',$userData['role_id'])->find()->toArray();
			$menuList = unserialize($menuData['menu_data']);
		}

		sort($menuList);
		return $menuList;
	}

	private function __getMenu()
	{
		$menuList = Config::load(CONF_PATH . DS . 'menu' . EXT)['menulist']; //config('routes');
		return $menuList;
	}


	private function _getUserMenu($id)
	{
		return $this->getUser($id);
	}


}

?>