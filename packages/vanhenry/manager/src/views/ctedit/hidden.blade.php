<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code?$default_code:array();
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
	}
 ?>
 <textarea class="hidden" name="{{$name}}" placeholder="{{FCHelper::ep($table,'note')}}"  dt-type="{{FCHelper::ep($table,'type_show')}}">{{$value}}</textarea>