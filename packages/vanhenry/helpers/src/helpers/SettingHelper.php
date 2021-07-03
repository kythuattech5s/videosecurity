<?php 
namespace vanhenry\helpers\helpers;
use DB;
use Illuminate\Support\Facades\Cache as Cache;
class SettingHelper{
	private static $listSettings = array();
	public static function getBladeConfig(){
		$def = app('config')->get('hsettings');
		return @$def?$def:array();
	}
	public static function getSettings(){
		$config = static::getBladeConfig();
		if(!Cache::has('_vh_configs_site')){
			$listSettings = DB::table('configs')->get();
			$tmp = array();
			foreach ($listSettings as $key => $value) {
				if(property_exists($value, 'name')){
					$tmp[$value->name] = $value;
				}
			}
			Cache::put('_vh_configs_site',$tmp,array_key_exists("time_cache_settings", $config)?$config['time_cache_settings']:100);
		}
		return Cache::get('_vh_configs_site');
	}
	public static function getSetting($key,$def=""){
		$key = trim($key);
		if(count(static::$listSettings)==0){
			static::$listSettings = static::getSettings();
		}
		$tmp = FCHelper::ep(static::$listSettings,$key,1,false);
		$ret =  FCHelper::ep($tmp,'value',1,false);
		if($ret=="value")
			return $def;
		else return $ret;
	}

	public static function getSettingImage($key,$subkey = "img",$def="admin/images/noimage.png"){
		$value = static::getSetting($key,$def);
		if($value!=$def){
			$json = json_decode($value,true);
			if(is_array($json) && array_key_exists("path", $json)){
				$hasWebp = \Config::get('app.webp');
				if($subkey=="img"){
					$img = $json["path"].$json["file_name"];
					// if ($hasWebp == true) {
					// 	$baseName = \Str::beforeLast($img, '.');
					// 	if (file_exists($baseName.'.webp')) {
					// 		$img = $baseName.'.webp';
					// 	}
					// }
					return $img;
				}
				else
				{
					return array_key_exists($subkey, $json)?$json[$subkey]:$def;
				}
			}
		}
		return $def;
	}

	public static function getSettingChooseLanguage($key,$lang){
		$key = trim($key);
		$config = \DB::table('configs')->where('name','=',$key)->first();
		if($lang == 'en'){
			return $config->en_value;
		}else{
			return $config->vi_value;
		}
	}
}