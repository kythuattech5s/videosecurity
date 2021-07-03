<?php 
$name = FCHelper::er($table,'name');
$value="";
if($actionType=='edit'){
	$value=FCHelper::er($dataItem,$name);
}
 ?>
<input type="hidden" name="{{$name}}" dt-type="{{FCHelper::er($table,'type_show')}}" value="{{$value}}">