<?php 

$name = FCHelper::er($table,'name');

$default_data = FCHelper::er($table,'default_data');

$default_data = json_decode($default_data,true);


$arrData = FCHelper::er($default_data,'data');

$arrConfig = FCHelper::er($default_data,'config');

$source = FCHelper::er($arrConfig,'source');


?>

@if(View::exists('vh::ctedit.select.'.$source))

@include('vh::ctedit.select.'.$source,array('arrData'=>$arrData))

@endif