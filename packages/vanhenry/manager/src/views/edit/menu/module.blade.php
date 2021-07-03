<?php 
$locale = \App::getLocale();
$value = FCHelper::er($dataItem,'table');
$value = isset($value) && $value!="" ? $value:"";
 ?>
<select class="menu-select">
	@foreach($showInMenu as $ksm =>$vsm)
		<option {{$value==$ksm?"selected":""}} value="{{$ksm}}">{{$vsm[$locale]}}</option>
	@endforeach
</select>