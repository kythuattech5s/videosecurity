<td data-title="{{$show->note}}">
	<?php $value =FCHelper::ep($itemMain,$show->name); ?>
	<span >{{array_key_exists($value,HaviHelper::$MIN_HOLE_SIZES)?HaviHelper::$MIN_HOLE_SIZES[$value][1]:''}}</span>

</td>