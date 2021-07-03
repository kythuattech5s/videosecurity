<?php
namespace vanhenry\manager\model;
use Illuminate\Database\Eloquent\Model;
use vanhenry\manager\controller\MultiPrimaryKeyTrait;
use DB;
class HRole extends Model
{
	use MultiPrimaryKeyTrait;
	protected $primaryKey = array('group_module_id','group_user_id');
	public $incrementing = false;
	public static function getModuleByUserId($uid){
		$results = DB::select('select a.*,b.table_map from h_modules a, h_group_modules b, h_roles c
			where b.act = 1 and a.parent = b.id and b.id = c.group_module_id
			and c.group_user_id = :uid and a.`code` & c.role >0', ['uid' => $uid]);
		return $results;
	}
	public static function getGroupModuleByUserId($uid){
		return DB::select('select a.* from h_group_modules a , h_roles b where a.act = 1 and a.id = b.group_module_id and b.role>0
			and  b.group_user_id = :uid',['uid'=>$uid]);
	}
}
