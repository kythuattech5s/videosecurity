<?php 
namespace vanhenry\manager\controller;
use \App\Question;
trait JBTrait{
	public function jbsearch(\Request $request,$table){
		if(request()->isMethod("post")){
			$inputs= request()->input();
			$cate = isset($inputs["cate"])?$inputs["cate"]:0;
			$name = isset($inputs["name"])?$inputs["name"]:"";
			$q = Question::select("id","code","name")->where("act",1)->whereRaw("(trash <> 1 or trash is null)");
			if($cate!=0){
				$q = $q->where("id_cate",$cate);
			}
			if($name!=""){
				$q= $q->where("name","LIKE","%".$name."%");
			}
			$arr = $q->get();
			return $arr->toJson();
		}
	}
}
?>