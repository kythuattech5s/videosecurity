<?php $data= $arrData;
$tableMap = $data['table'];
$dataMapDefault = $data['default'];
$isAjax = (isset($default_data['config']['ajax'])?$default_data['config']['ajax']:"")==1;
$intersectData1 =array();
$intersectData2 =array();
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
	$dataMap = vanhenry\manager\helpers\DetailTableHelper::getAllDataOfTable($tableMap);
	$arrValue = explode(',', $value);
	$intersectData1 = array_intersect($arrValue, array_keys($dataMapDefault));
	$intersectData2 = array_intersect($arrValue, array_keys($dataMap));
}
?>
<div class="form-group ">
  <p class="form-title" for="">{{FCHelper::ep($table,'note')}}<p/>
  <div class="form-control form-reset flex">
    <select {{FCHelper::ep($table,'require')==1?'required':''}} style="width:100%" placeholder="{{FCHelper::ep($table,'note')}}" class=" {{$isAjax?'ajx_search_single_'.$name:'select2'}}" name="{{$name}}">
     @if($isAjax)
    	@if($actionType=='edit')
    		@foreach($intersectData1 as $id1 =>$vid1)
    		<option selected value="{{$vid1}}">{{FCHelper::ep($dataMapDefault[$vid1],"value")}}</option>
    		@endforeach
    		@foreach($intersectData2 as $id2 =>$vid2)
    		<option selected value="{{$vid2}}">{{$dataMap[$vid2]->name}}</option>
    		@endforeach
    	@endif
    @else
    	<?php 
    		$arrData = vanhenry\manager\helpers\DetailTableHelper::recursiveDataTable($default_data['data']);

    		vanhenry\manager\helpers\DetailTableHelper::printOptionRecursiveData($arrData,0,$dataMapDefault,$intersectData1,$intersectData2,$default_data['data']);
    	?>
    @endif
	</select>
  </div>
  
</div>
@if($isAjax)
<script type="text/javascript">
$(function() {
	$('.ajx_search_single_{{$name}}').select2({
	   ajax: {
	    url: "{{$admincp}}/getData/{{$tableMap}}",
	    dataType: 'json',
	    data: function (params) {
	      return {
	        q: params.term, 
	        page: params.page
	      };
	    },
	    processResults: function (data, page) {
	      return data;
	    },
	    cache: true
	  },
	  minimumInputLength: 1,
	  language:"{{App::getLocale()}}",
});
});
	
</script>
@endif