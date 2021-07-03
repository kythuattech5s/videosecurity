<?php 
namespace vanhenry\customblade;
use DB;
use Illuminate\Support\Facades\Cache as Cache;
class BladeData{
	public static function getBladeConfig(){
		$def = app('config')->get('hblade');
		return $def;
	}
	public static function getAllBladeMap(){
		// if(!Cache::has('v_blade')){
		// 	$config = static::getBladeConfig();
			$ret = DB::table('v_blademap')->where('act',1)->get();
		// 	Cache::put('v_blade',$ret,$config['time_cache']);
		// }
		// $ret = Cache::get('v_blade',array());
		return $ret;
	}
}
?>