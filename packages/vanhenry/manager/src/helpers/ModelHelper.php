<?php 
namespace vanhenry\manager\helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Schema;
class ModelHelper
{
	public static function delete($inputs,$table){
		if(@$inputs['id']){
			$model = GlobalHelper::getModel($table);
			if(!$model)
			{
				$ret = DB::table($table)->where('id',$inputs['id'])->delete();
			}
			else{
				$ret = $model->where('id',$inputs['id'])->delete();
			}
			$table_meta = $table."_metas";
			if(Schema::hasTable($table_meta)){
				DB::table($table)->where('source_id',$inputs['id'])->delete();
			}
			return 200;
		}
		else{
			return 100;
		}
	}
	public static function trash($inputs,$table,$value=1){
		if(@$inputs['id']){
			$model = GlobalHelper::getModel($table);
			$dataUpdate = array("trash"=>$value);
			if(!$model)
			{
				$ret = DB::table($table)->where('id',$inputs['id'])->update($dataUpdate);
			}
			else{
				$ret = $model->where('id',$inputs['id'])->update($dataUpdate);
			}
			\Event::dispatch('vanhenry.manager.trash.success', array($table,$inputs['id'],$value));
			return 200;
		}
		else{
			return 100;
		}
	}
	public static function update( $table,$id,$dataUpdate){
		if(isset($id)){
			$model = GlobalHelper::getModel($table);
			if(!$model)
			{
				$ret = DB::table($table)->where('id',$id)->update($dataUpdate);
			}
			else{
				$ret = $model->where('id',$id)->update($dataUpdate);
			}
			return 200;
		}
		else{
			return 100;
		}
	}
}
?>