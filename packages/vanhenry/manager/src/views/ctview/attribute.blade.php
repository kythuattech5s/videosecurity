<?php
$defaultData = json_decode(FCHelper::ep($show,"default_data"), true);
$listData = [];
if (is_array($defaultData)) {
	$pivotTable = $defaultData['pivot_table'];
	$originField = $defaultData['origin_field'];
	$targetTable = $defaultData['target_table'];
	$targetField = $defaultData['target_field'];
	$targetSelect = $defaultData['target_select'];
	$columns = [];
	foreach ($targetSelect as $key => $value) {
		$columns[] = $value;
	}
	$dataPivots = FCHelper::getDataPivot($pivotTable, $originField, $targetTable, $targetField, $columns, $dataItem->id);
}
?>
<td data-title="{{$show->note}}">
	@if(in_array('parent', $columns))
		{{FCHelper::viewRecursivePivotPrint($dataPivots, 0)}}
	@else
		{{FCHelper::viewPivotPrint($dataPivots)}}
	@endif
</td>