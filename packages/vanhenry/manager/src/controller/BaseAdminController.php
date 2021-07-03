<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use Session;
use Illuminate\Support\Collection ;
use vanhenry\manager\helpers\CT;
use DB;
class BaseAdminController extends BaseController
{
	protected $admincp;
	protected $userglobal;
	public function __construct(){
		$this->admincp = \Config::get('manager.admincp');
		view()->share('admincp',$this->admincp);
		$this->userglobal = Session::get(CT::$KEY_SESSION_USER_LOGIN);
		view()->share('userglobal',$this->userglobal);
		view()->share('locales', \Config::get('app.locales'));
	}
	protected function retreiveUser($key = null){
		$user = Session::get(CT::$KEY_SESSION_USER_LOGIN)['user'];
		if(is_null($key)){
			return $user;
		}
		else{
			return $user->$key;	
		}
	}
	protected function pullSession($key=null){
		if(!is_null($key)){
			return Session::pull($key);
		}
	}
	protected function __getInfoGroup(){
		$arrGroup =DB::table('v_regions')->get();
		$ret = array();
		foreach ($arrGroup as $key => $value) {
			$ret[$value->id] = $value;
		}
		return $ret;
	}
	protected function __groupByRegion($tableDetailData){
		$ret = $tableDetailData;
		if(!$tableDetailData instanceof Collection){
			$ret = new Collection($tableDetailData);
		}
		$ret = $ret->groupBy('region');
		return $ret;
	}
	protected function __groupByGroup($tableDetailData){
		$ret = $tableDetailData;
		if(!$tableDetailData instanceof Collection){
			$ret = new Collection($tableDetailData);
		}
		$ret = $ret->groupBy('group');
		return $ret;
	}
}