<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code?$default_code:array();
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::ep($dataItem,$name);
}
 ?>
<p class="des col-xs-12">Chọn 1 hình làm đại diện</p>
<div class="col-xs-12">
<img src="{{$value}}" alt="" class="img-responsive">
<input placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}}  type="hidden" value="{{$value}}" name="{{$name}}">
<div class="form-group textcenter">
  <button class="browseimage bgmain btn btn-primary">Chọn hình</button>
</div>
</div>