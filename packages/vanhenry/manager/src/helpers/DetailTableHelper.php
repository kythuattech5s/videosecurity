<?php 
namespace vanhenry\manager\helpers;
use Illuminate\Support\Facades\Cache as Cache;
use FCHelper;
use DB;
use App;
use vanhenry\manager\model\HGroupUser;
class DetailTableHelper
{
	public static function filterSimpleSearch($collect){
		$ret = $collect->filter(function ($item)  {
	    	return FCHelper::er($item,'advance_search') !=1 && FCHelper::er($item,'simple_search') ==1;
	    })->sortByDesc('id')->first();
	    if($ret == null){
	    	$ret = $collect->filter(function ($item)  {
	    		return FCHelper::er($item,'type_show') == 'PRIMARY_KEY';
	    	})->sortByDesc('id')->first();
	    }
	    return $ret; 
	}
	public static function filterSimpleSort($collect){
		$ret = $collect->filter(function ($item)  {
	    	return FCHelper::er($item,'simple_sort') ==1 || FCHelper::er($item,'type_show')=='PRIMARY_KEY';
	    }); 
	    return $ret;
	}
	public static function filterAdvanceSearch($collect){
		$ret = $collect->filter(function ($item)  {
	    	return FCHelper::er($item,'advance_search') ==1 && FCHelper::er($item,'simple_search') !=1;
	    }); 
	    return $ret;
	}
	public static function filterShow($collect){
		$ret = $collect->filter(function ($item)  {
	    	return FCHelper::er($item,'show') ==1 ;
	    }); 
	    return $ret;
	}
	public static function filterDataShow($collect,$force=false){
		
		if($force) return $collect;
		$ret = $collect->filter(function ($item)  {
			return $item->show ==1 || $item->type_show =='PRIMARY_KEY' ;
	    }); 
	    return $ret;
	}
	public static function transaction(Closure $callback){
		DB::beginTransaction();
		try {
			
			$callback();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
		}
	}
	public static function getAllDataOfTable($table){
		$ret =Cache::remember(CT::$KEY_GET_ALL_TABLE.$table, CT::$TIME_CACHE_GET_ALL_TABLE, function() use($table) {
		    $tmp = DB::table($table)->get();
		    $r = array();
		    foreach ($tmp as $key => $value) {
		    	$r[$value->id] = $value;
		    }
		    return $r;
		});
		return $ret;
	}
	public static function getSomeFielDataOfTable($table,$select,$where){
		$ret =Cache::remember(CT::$KEY_GET_ALL_SFIELD_TABLE.$table, CT::$TIME_CACHE_GET_ALL_SFIELD_TABLE, function() use($table) {
		    $tmp = DB::table($table)->get();
		    $r = array();
		    foreach ($tmp as $key => $value) {
		    	$r[$value->id] = $value;
		    }
		    return $r;
		});
		return $ret;
	}
	public static function recursiveDataTable($data){
		$table = $data['table'];
		$select = explode(',',$data['select']);
		$fBase = $data['field_base'];
		$fRoot = $data['field_root'];
		$fValue = $data['first_value'];
		$_where = isset($data['where']) && is_array($data['where'])?$data["where"]:array();
		$where= array();
		foreach ($_where as $k => $v) {
			foreach ($v as $key => $value) {
				$where[$key]=$value;
			}
		}
		return self::tempRecursiveDataTable($table,$select,$fBase,$fRoot,$fValue,$where);
	}
	private static function tempRecursiveDataTable($table,$select,$fBase,$fRoot,$fValue,$where){
		if(isset($table) && $table!=""){
			$transTable = FCHelper::getTranslationTable($table);
			if ($transTable != null) {
				$langChoose = FCHelper::langChooseOfTable($table);
				$q = DB::table($table)->select($select)->join($transTable->table_map, 'id', '=', 'map_id')->where('language_code', $langChoose);
			}
			else{
				$q = DB::table($table)->select($select);
			}
			if(count($where)>0){
				$q= $q->where($where);
			}
			if(isset($fValue) && $fValue!="-1"){
				$q = $q->where($fRoot,$fValue);
			}
			$arr = $q->orderBy("name","asc")->get();
			foreach ($arr as $key => $value) {
				if(property_exists($value, $fBase))
				{
					$value->childs = self::tempRecursiveDataTable($table,$select,$fBase,$fRoot, $value->$fBase,$where);
				}
			}
			return $arr;
		}
		else{
			return array();
		}
	}
	public static function printOptionRecursiveData($arr,$lv=0,$dataDefault=array(),$interDefault=array(),$interDB = array(),$data = null){
		$dataDefault = isset($dataDefault)?$dataDefault:array();
		if($lv==0){
			foreach ($dataDefault as $key => $value) {
				$tmp = FCHelper::ep($value,"value");
				echo "<option ".(in_array($key, $interDefault)?"selected":"")." value='".$key."'>".str_repeat('--',$lv).$tmp."</option>";
			}
		}
		if($data !== null){
			$dataSelect = explode(',', $data['select']);
			
			if($dataSelect[1] == 'name'){
				$mainSelect = 'name';
			}else{
				$mainSelect = $dataSelect[1];
			}
		}else{
			$mainSelect = 'name';
		}

		foreach ($arr as $key => $value) {
			echo "<option ".(in_array($value->id, $interDB)?"selected":"")." value='".$value->id."'>".str_repeat('--',$lv).$value->$mainSelect."</option>";
			if( property_exists($value, "childs") &&  count($value->childs)>0){
				self::printOptionRecursiveData($value->childs,$lv+1,$dataDefault,$interDefault,$interDB);
			}
		}
	}
	public static function recursiveGroupUser($id,$selected,$lv=0){
			$grs = HGroupUser::select("id","name")->where("parent",$id)->get();
			foreach ($grs as $key => $value) {
				echo "<option ".($selected==$value->id?'selected':'')." value=".$value->id.">".str_repeat("--", $lv).$value->name."</option>";
				static::recursiveGroupUser($value->id,$lv++);
			}
		
	}
}
?>