<td data-title="{{$show->note}}">	<?php $value =FCHelper::ep($dataItem,$show->name); ?>	<span >{{array_key_exists($value,HaviHelper::$MATERIALS)?HaviHelper::$MATERIALS[$value][1]:''}}</span></td>