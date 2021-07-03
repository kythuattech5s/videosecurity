<?php 
namespace vanhenry\manager\helpers;
use Illuminate\Database\Eloquent\Model;
class GlobalHelper
{
	public static  function getModel($name){
		$name = \Str::studly(\Str::singular($name));
		if(class_exists('App\\'.$name)){
			$class = "App\\".$name;
			return new $class();
		}
		else if(class_exists('\\vanhenry\\manager\\model\\'.$name)){
			$class = "\\vanhenry\\manager\model\\".$name;
			return new $class();
		}
		else
		{
			return false;
		}
	}
	public static function printMenu($admincp,$tableData,$arr){
		echo "<ul>";
		foreach ($arr as $key => $value) {
			echo "<li>";
            echo "<a href='".$admincp."/edit/".$tableData->get('table_map','')."/".$value->id."' title=''>".$value->name."</a>";
            static::printMenu($admincp,$tableData,$value->childs);
            echo "</li>";
		}
		echo "</ul>";
	}
}
?>