<td data-title="{{$show->note}}">

<?php $defaultData = FCHelper::ep($dataItem,$show->default_data); 

$arrKey = json_decode($defaultData,true);

$arrData = FCHelper::er($arrKey,'data');

$arrConfig = FCHelper::er($arrKey,'config');

$source = $arrConfig['source']; 

?>

@include('vh::ctview.select.'.$source,array('arrData'=>$arrData))

</td>