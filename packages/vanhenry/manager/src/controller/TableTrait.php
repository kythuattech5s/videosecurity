<?php 
namespace vanhenry\manager\controller;
use vanhenry\helpers\helpers\StringHelper as StringHelper;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use Carbon\Carbon;
use vanhenry\manager\helpers\CT as CT;
use FCHelper;
trait TableTrait{
	public static function __getListTable(){
		$expiresAt = Carbon::now()->addMinutes(10);
		$tmpTable = Cache::remember('_vh_admin_listTable', $expiresAt, function() {
			$listTable = DB::table('v_tables')->orderBy('ord','ASC')->get();
			$_t =array();
			foreach ($listTable as $key => $value) {
				$_t[$value->table_map] = $value;
			}
		    return $_t;
		});
		return $tmpTable;
	}
	public static function __getListDetailTable($table){
		$listDetail = Cache::has('_vh_admin_listDetailTable')?Cache::get('_vh_admin_listDetailTable'):array();
		if(!array_key_exists($table, $listDetail)){
			$listDetailTable = DB::table('v_detail_tables')->where("act",1)->where(function($q) use($table){
				$q->where('parent_name',$table);
				$transTable = FCHelper::getTranslationTable($table);
				if ($transTable != null) {
					$q->orWhere('parent_name', $transTable->table_map);
				}
			})->orderBy('ord','ASC')->get();
			$listDetail[$table] = $listDetailTable->filter(function($v, $k) use($table){
				if ($v->parent_name != $table) {
					return $v->name != 'map_id' && $v->name != 'language_code';
				}
				return true;
			});
		}
		$expiresAt = Carbon::now()->addMinutes(CT::$TIME_CACHE_QUERY);
		Cache::put('_vh_admin_listDetailTable', $listDetail, $expiresAt);
		$listDetail = Cache::get('_vh_admin_listDetailTable');
		return $listDetail[$table];
	}
	public static function checkExistTable($table){
		if(StringHelper::isNull($table)){
			return false;
		}
		$listTable = self::__getListTable();
		return array_key_exists($table, $listTable);
	}
	public static function dataTranslations($tableTranslationName, $dataitem, $locale)
	{
		$fields = \DB::table('v_detail_tables')->where('parent_name', $tableTranslationName)->get()->toArray();
		if (count($fields) == 0) {
			return [];
		}
		$fields = \Arr::where($fields, function($value, $key){
			return $value->name != 'map_id' && $value->name != 'language_code';
		});
		$tmpFields = [];
		foreach ($fields as $field) {
			$obj = new \stdClass;
			foreach ($field as $key => $value) {
				if ($key == 'name' && $value != 'map_id' && $value != 'language_code') {
					$obj->$key = $value.'_'.$locale;
				}
				else{
					$obj->$key = $value;	
				}
			}
			$tmpFields[] = $obj;
		}
		$fields = $tmpFields;
		$tmpDataItem = new \stdClass;
		if (is_object($dataitem)) {
			$data = \DB::table($tableTranslationName)->where(['map_id' => $dataitem->id, 'language_code' => $locale])->first();
			foreach ($data as $key => $value) {
				if ($key != 'map_id' && $key != 'language_code') {
					$newKey = $key.'_'.$locale;
					$tmpDataItem->$newKey = $value;
				}
			}
		}
		$dataitemTranslation = $tmpDataItem;
		return compact('fields', 'dataitemTranslation');
	}
	public function tableLang($table, $locale)
	{
		$sTableLang = \Session::get('_table_lang');
		if (!is_array($sTableLang) || !array_key_exists($table, $sTableLang) || $sTableLang[$table] != $locale) {
			$tableLangs[$table] = $locale;
			\Session::put('_table_lang', $tableLangs);
		}
		return redirect()->back();
	}
}
?>