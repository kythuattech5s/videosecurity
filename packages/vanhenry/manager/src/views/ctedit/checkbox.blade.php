<?php 

$name = FCHelper::er($table,'name');

$defaultData = FCHelper::er($table,'default_data');

$arrKey = json_decode($defaultData,true);

$arrKey = FCHelper::er($arrKey,'data');

$value ="";

if($actionType=='edit'||$actionType=='copy'){

	$value = FCHelper::ep($dataItem,$name);

}

if(!isset($arrKey) || $arrKey ==null || $arrKey =="data"){

	echo "Chưa set default data";

	return;

}

?>



<div class="form-group">

  <p class="form-title" for="">{{FCHelper::ep($table,'note')}} <span class="count"></span></p>

  <select placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}}  class="simpleselect2 w100" name="{{$name}}" dt-type="{{FCHelper::er($table,'type_show')}}" >
	@foreach($arrKey as $key => $v)

		@if(is_array($v))

			<option {{FCHelper::ep($v,'key',1)==$value?'selected':''}} value="{{FCHelper::ep($v,'key',1)}}">{{FCHelper::ep($v,'value',1)}}</option>

		@else

			<option {{$v==$value?'selected':''}} value="{{$v}}">{{$key}}</option>

		@endif

	@endforeach

	</select>

</div>

