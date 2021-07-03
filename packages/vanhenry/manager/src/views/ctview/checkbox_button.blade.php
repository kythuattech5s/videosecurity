<?php 
$value =FCHelper::ep($dataItem,$show->name);
?>
<td data-title="{{$show->note}}">
  <input dt-prop="{{$show->is_prop ?? 0}}" dt-prop-id="{{$show->id}}" type="checkbox" data-off-label="false" data-on-label="false" data-off-icon-cls="glyphicon-remove" name="{{$show->name}}" {{$value == 1?'checked':''}} data-on-icon-cls="glyphicon-ok" class="ccb {{$show->editable==1?'editable':''}}" />
</td>
