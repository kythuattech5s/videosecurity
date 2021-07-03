<?php 
$table = FCHelper::ep($arrData,'table');
$where = FCHelper::ep($arrData,'where');
$select = FCHelper::ep($arrData,'select');
$field_base = FCHelper::ep($arrData,'field_base');
$default = FCHelper::ep($arrData,'default');
$simpleDefault = array();
foreach ($default as $_key => $_value) {
	array_push($simpleDefault, $_key);
}
$select = vanhenry\helpers\helpers\StringHelper::isNull($select)?"id,name":$select;
$select = explode(',', $select);
$fullSelect = array();
$select = array_merge($select,$fullSelect);
$database = Config::get('database.connections.'.Config::get('database.default').'.database');
$info_schema = DB::select("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$database."' AND TABLE_NAME = '".$table."' AND COLUMN_NAME IN('".implode(',', $select)."')");
$isMultiselect = FCHelper::ep($arrConfig,'multiselect');
$arrTmp = Cache::remember('_vh_admin_view_select_'.$tableData->get($table,'')."_".$show->name, 10, function() use($select,$where,$table,$transTable) {
	if ($transTable != null) {
		$langChoose = FCHelper::langChooseOfTable($table);
		$q = DB::table($table)->select($select)->join($transTable->table_map, 'id', '=', 'map_id')->where('language_code', $langChoose);
	}
	else{
		$q = DB::table($table)->select($select);
	}
	if($where !=null && count($where)>0){
		$q = $q->where(array($where));
	}
	return $q->get();
}); 
?>
<?php 
	$currentID = FCHelper::ep($dataItem,$show->name);
?>
<?php $multi = explode(',', $currentID); $arrMerge = array_intersect($multi,$simpleDefault); ?>
@if($show->editable == 1 && !$isMultiselect)
<select dt-prop="{{$show->is_prop ?? 0}}" dt-prop-id="{{$show->id}}" name="{{$show->name}}" class="select2 editable" table="{{$show->parent_name}}" style="width: 150px">
	<option value="0">Không xác định</option>
	@foreach($arrTmp as $key => $value)
	<option value="{{$value->id}}" {{$value->id == $currentID?"selected":""}}>{{ $value->name}}</option>
	@endforeach
</select>
@else
	@if(count($arrMerge)>0)
		@foreach($arrMerge as $m)
		<p class="select static-select" dt-value="{{$m}}">{{FCHelper::ep($default[$m],'value',1)}}</p>
		@endforeach
	@endif
	@foreach($arrTmp as $key => $value)
		@if($isMultiselect)
			@if(in_array($value->id,$multi))
			<p class="select static-select" dt-value="{{$value->id}}">{{ FCHelper::ep($value,$select[1]) }}</p>
			@endif
		@else
			@if($value->id == $currentID)
			<p class="select static-select" dt-value="{{$value->id}}">{{ FCHelper::ep($value,$select[1]) }}</p>
			@else
				@if(array_key_exists($currentID,$default))
				<p class="select static-select" dt-value="{{$currentID}}">{{FCHelper::ep($default,'value',1)}}</p>
				@endif
			@endif
		@endif
	@endforeach
@endif