<?php 
namespace vanhenry\helpers\helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Schema;
class AdminMenuHelper
{
	public static function getAllTableHasSlug(){
		$lst = DB::select("select table_map as name, note from v_tables where show_menu = 1");
		$language = \Session::get('_table_lang');
		if($language == null){
			$lang = 'vi';
		}else{
			$lang = $language[array_key_first($language)];
		}
		foreach ($lst as $key => $item) {
			$name = $item->name;
			$transTable = FCHelper::getTranslationTable($name);
			if ($transTable != null) {
				$children = DB::table($name)->join($transTable->table_map, 'id', '=', 'map_id')->select("id","name","slug")->where("act",1)->get();
			}
			else{
				$children = DB::table($name)->select("id","name","slug")->where("act",1)->get();
			}
			$item->children = $children;
		}
		return $lst;
	}
}