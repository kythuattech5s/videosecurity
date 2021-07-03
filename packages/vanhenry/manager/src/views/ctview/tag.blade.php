<?php 



$defaultData = FCHelper::er($show,'default_data');

$arrKey = json_decode($defaultData,true);

$arrKey = FCHelper::er($arrKey,'data');

$_dbTable = $arrKey['table'];

$_dbField = explode(',',$arrKey['select']);

$__dbWhere = $arrKey['where'];

$_dbWhere = array();

foreach ($__dbWhere as $key => $vv) {

	$tmp = array();

	foreach ($vv as $k => $v) {

		array_push($tmp,$k);

		array_push($tmp,$v);

	}

	array_push($_dbWhere, $tmp);

}

/*giá trị trong DB*/



$arrTags=vanhenry\manager\helpers\DetailTableHelper::getSomeFielDataOfTable($_dbTable,$_dbField,$_dbWhere);

$value = FCHelper::ep($dataItem,$show->name);

$value = explode(',',$value);

$intersectData1 = array_intersect($value,array_keys($arrTags));

 ?>

<td data-title="{{$show->note}}">

	@foreach($intersectData1 as $id1 =>$vid1)

	<p class="select static-select" dt-value="{{FCHelper::er($arrTags[$vid1],'id')}}">{{FCHelper::er($arrTags[$vid1],'name')}}</p>

	@endforeach

</td>