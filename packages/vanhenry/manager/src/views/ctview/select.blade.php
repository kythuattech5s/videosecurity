<td data-title="{{$show->note}}">
<?php $defaultData = FCHelper::ep($show,"default_data"); 
$arrKey = json_decode($defaultData,true);
$arrData = FCHelper::er($arrKey,'data');
$arrConfig = FCHelper::er($arrKey,'config');
$source = FCHelper::er($arrConfig,'source'); 
?>
@if(View::exists('vh::ctview.select.'.$source))
@include('vh::ctview.select.'.$source,array('arrData'=>$arrData))
@endif
</td>