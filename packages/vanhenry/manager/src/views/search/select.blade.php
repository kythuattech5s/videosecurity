<?php
$defaultData = FCHelper::er($item,'default_data');
$arrKey = json_decode($defaultData,true);
$data = FCHelper::er($arrKey,'data');
$config = FCHelper::er($arrKey,'config');
$source = $config['source'];
?>
@if(View::exists('vh::search.select.'.$source))
@include('vh::search.select.'.$source)
@endif