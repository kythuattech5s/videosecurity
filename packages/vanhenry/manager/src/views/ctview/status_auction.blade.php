<td data-title="{{$show->note}}">
	@if(new DateTime < new DateTime($dataItem->start_time))
	<span class="text-info">Sắp diễn ra</span>
	@elseif(new DateTime >= new DateTime($dataItem->start_time) && new DateTime <= new DateTime($dataItem->expired_time))
	<span class="text-success">Đang diễn ra</span>
	@else
	<span class="text-danger">Đã kết thúc</span>
	@endif
</td>