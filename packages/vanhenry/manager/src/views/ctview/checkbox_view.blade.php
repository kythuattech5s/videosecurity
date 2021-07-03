<td data-title="{{$show->note}}">

	<a href="{{$admincp}}/edit/{{$tableData->get('table_map','')}}/{{FCHelper::ep($dataItem,'id')}}?returnurl={{base64_encode(Request::fullUrl())}}" target="_blank">Xem chi tiết</a> </td>