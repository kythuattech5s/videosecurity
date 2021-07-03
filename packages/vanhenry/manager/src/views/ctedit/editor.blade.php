<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code&& count($default_code)>0?$default_code[0]:array();
$height = FCHelper::er($default_code,'height');
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
}
 ?>
<div class="form-group">
  <p class="form-title" for="">{{FCHelper::er($table,'note')}}</p>
  <textarea placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}}  dt-height="{{$height}}" name="{{$name}}" class="form-control editor" rows="5" dt-type="{{FCHelper::er($table,'type_show')}}">{{$value}}</textarea>
</div>