<td data-title="{{$show->note}}">

	<?php $value =FCHelper::ep($dataItem,$show->name) ?>

	@if(!empty($value))

	<a target="_blank" style="display: inline-block;padding: 5px;text-transform: uppercase;background: #00923f;color: #fff;margin: 5px 0px;" href="{{asset('/')}}{{FCHelper::ep($dataItem,$show->name)}}"><i class="fa fa-download"></i></a>

	@endif

</td>