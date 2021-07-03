<?php /* 
static data store in database, resolve to $arrData
compare each value with current value in $dataItem
if exist -> show as tag p
{
	"data": {
		"Undefined": {
			"key": "-1",
			"en_value": "Undefined",
			"vi_value": "Không xác định"
		},
		"No": {
			"key": "0",
			"en_value": "No",
			"vi_value": "Không"
		},
		"Yes": {
			"key": "1",
			"en_value": "Yes",
			"vi_value": "Có"
		}
	},
	"config": {
		"source": "static",
		"has_search": "1",
		"multiselect": "1"
	}
}
*/ ?>
<?php $isMultiselect = FCHelper::ep($arrConfig,'multiselect'); ?>
@if($show->editable == 1 && !$isMultiselect)
	<select name="{{$show->name}}" class="select2 editable" table="{{$show->parent_name}}" style="width: 150px">
		@foreach($arrData as $key => $value)
		<?php 
			$tmpValue = is_array($value) ? FCHelper::ep($value,'key',1): $value ; 
			$currentID = FCHelper::ep($dataItem,$show->name);
		?>
			<option {{$tmpValue == $currentID?"selected":""}} value="{{$tmpValue}}">{{ is_array($value) ? FCHelper::ep($value,'value',1): $key}}</option>
		@endforeach
	</select>
@else
	@foreach($arrData as $key => $value)
		<?php 
		$tmpValue = is_array($value) ? FCHelper::ep($value,'key',1): $value ; 
		$currentID = FCHelper::ep($dataItem,$show->name);
		?>
		@if($isMultiselect)
			<?php $multi = explode(',', $currentID); ?>
			@if(in_array($tmpValue,$multi))
				<p class="select static-select" dt-value="{{$tmpValue}}">{{ is_array($value) ? FCHelper::ep($value,'value',1): $key}}</p>
			@endif
		@else
			@if($tmpValue == $currentID)
				<p class="select static-select" dt-value="{{$tmpValue}}">{{ is_array($value) ? FCHelper::ep($value,'value',1): $key}}</p>
			@endif
		@endif
	@endforeach
@endif