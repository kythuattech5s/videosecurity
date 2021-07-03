<?php 
$name = FCHelper::er($table,'name');
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
}
 ?>
<input placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}} type="text" name="{{$name}}" value="{{$value}}" dt-type="{{FCHelper::er($table,'type_show')}}" class="form-control">