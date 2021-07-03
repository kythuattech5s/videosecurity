<td data-title="{{$show->note}}">

<?php $defaultData = FCHelper::ep($dataItem,$show->default_data); 

$arrKey = json_decode($defaultData,true);

$arrKey = FCHelper::er($arrKey,'data');

?>

<select dt-prop="{{$show->is_prop ?? 0}}" dt-prop-id="{{$show->id}}" class="{{$show->editable==1?'editable':''}}" name="{{$show->name}}">

	@foreach($arrKey as $key => $value)

		@if(is_array($value))

			<option {{ FCHelper::ep($dataItem,$show->name)==FCHelper::ep($value,'key',1)?'selected':'' }} value="{{FCHelper::ep($value,'key',1)}}">{{FCHelper::ep($value,'value',1)}}</option>

		@else

			<option {{ FCHelper::ep($dataItem,$show->name)==$value?'selected':'' }} value="{{$value}}">{{$key}}</option>

		@endif

	@endforeach

</select>

</td>