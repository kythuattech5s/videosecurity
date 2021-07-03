<?php $defaultData = FCHelper::er($item,'default_data');
$arrKey = json_decode($defaultData,true);
$arrKey = FCHelper::er($arrKey,'data');
?>
<div class="search-item search-{{$item->name}} search-type-text flex">
	<select class="simpleselect2 w100" name="{{$item->name}}">
	@foreach($arrKey as $key => $value)
		@if(is_array($value))
			<option value="{{FCHelper::ep($value,'key',1)}}">{{FCHelper::ep($value,'value',1)}}</option>
		@else
			<option value="{{$value}}">{{$key}}</option>
		@endif
	@endforeach
	</select>
</div>