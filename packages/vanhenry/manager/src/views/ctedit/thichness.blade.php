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
<div class="form-group" @if($tableMap=='orders' && $name=='val_order') style="display: none;" @endif>
	<p class="form-title" for="">{{FCHelper::ep(($tableMap=='configs'?$dataItem:$table),'note')}} <span class="count"></span></p>
	
	<select  class="form-control" name="{{$name}}" >
		@foreach(HaviHelper::$THICHNESS as $k => $thichness)
		<option {{$value == $k?'selected':''}} value="{{$k}}">{{$thichness[1]}}</option>
		@endforeach
	</select>
	
</div>