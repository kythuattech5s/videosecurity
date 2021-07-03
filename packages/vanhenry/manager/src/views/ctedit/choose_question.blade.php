<?php 

$name = FCHelper::er($table,'name');

$default_code = FCHelper::er($table,'default_code');

$default_code = json_decode($default_code,true);

$default_code = @$default_code?$default_code:array();

$value ="";

$img = 'admin/images/noimage.png';

if($actionType=='edit'||$actionType=='copy'){

	$value = FCHelper::ep($dataItem,$name);

	$tmp = json_decode($value,true);

	$img = isset($tmp) && is_array($tmp)  ?$tmp["path"].$tmp["file_name"]:$img; 

}

 ?>

<p class="des col-xs-12">Tìm trong ngân hàng câu hỏi</p>

<div class="col-xs-12">

	<div class="col-md-6 col-xs-12">

			TÌM KIẾM 

	</div>

	<div class="col-md-6 col-xs-12"></div>

</div>