<?php 
namespace vanhenry\customblade;
class BladeFunction{
	public static function ep($item,$key,$checklang){
		if(isset($item) && isset($key)){
			if(is_array($item) && array_key_exists($key,$item)){
				return $item[$key];
			}
			return $key;
		}
		return "";
	}
	public static function ec($item,$key,$checklang){
		echo BladeFunction::ep($item,$key,$checklang);
	}
}

 ?>