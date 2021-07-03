<td data-title="{{$show->note}}">
	<input dt-prop="{{$show->is_prop ?? 0}}" dt-prop-id="{{$show->id}}" class="{{$show->editable==1?'editable':''}}"  name="{{$show->name}}" title="{{$show->note}}" value="{{FCHelper::ep($dataItem,$show->name)}}" type="text" disabled />
</td>