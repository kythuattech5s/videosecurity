<?php 
$name = FCHelper::er($table,'name');
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::ep($dataItem,$name);
}
?>
<div class="tooltipx">
  <input type="checkbox" onchange="$(this).next().val($(this).is(':checked')?1:0)">
  <input type="hidden" name="{{$name}}" value="{{$value}}">
  <span class="tooltiptext">{{FCHelper::er($table,'note')}} </span>
</div>