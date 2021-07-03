<?php 
$name = FCHelper::er($table,'name');
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
}
if($name != 'infor_order') {
	?>
	<div style="border-bottom: 1px dotted; line-height: 25px;">{{FCHelper::er($table,'note')}}: <strong>{!!$value!!}</strong></div>
	<?php } ?>