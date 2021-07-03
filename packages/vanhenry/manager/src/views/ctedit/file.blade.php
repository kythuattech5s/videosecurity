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



	<input  {{FCHelper::ep($table,'require')==1?'required':''}} type="text" name="{{$name}}" placeholder="{{FCHelper::ep($table,'note')}}"  class="form-control" dt-type="{{FCHelper::ep($table,'type_show')}}" value="{{$value}}" />

	@if($actionType=='edit'||$actionType=='copy')

	<a style="display: inline-block;padding: 5px;text-transform: uppercase;background: #00923f;color: #fff;margin: 5px 0px;" href="{{asset('/')}}{{$value}}" target="_blank">Download</a>

	@endif
	

</div>