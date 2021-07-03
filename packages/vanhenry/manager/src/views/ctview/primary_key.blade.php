<td data-title="{{$show->note}}">

	<p dt-prop="{{$show->is_prop ?? 0}}" dt-prop-id="{{$show->id}}" class="{{$show->editable==1?'editable':''}}"  name="{{$show->name}}" title="{{$show->note}}">{{FCHelper::ep($dataItem,$show->name)}}</p>

</td>