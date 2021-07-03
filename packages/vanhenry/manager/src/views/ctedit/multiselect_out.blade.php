<?php 


$name = FCHelper::er($table,'name');


$default_data = FCHelper::er($table,'default_data');


$default_data = json_decode($default_data,true);


$data= $default_data['data'];


$tableMap = $data['table'];


$dataMapDefault = $data['default'];


$isAjax = (isset($default_data['config']['ajax'])?$default_data['config']['ajax']:"")==1;


$intersectData1 =array();


$intersectData2 =array();


$value ="";


if($actionType=='edit'||$actionType=='copy'){


	$id = FCHelper::er($dataItem,"id");


	$tableRef = FCHelper::er($table,'more_note');


	$tableName  = FCHelper::er($table,'parent_name');


	$tableMapOut = $tableName."_".$tableRef;


	if(!\Schema::hasTable($tableMapOut)){


		$tableMapOut = $tableRef."_".$tableName;


	}


	if(\Schema::hasTable($tableMapOut)){


		$tmp = \DB::table($tableMapOut)->where(\Str::singular($tableName)."_id",$id)->get();


		$value =collect($tmp)->implode(\Str::singular($tableRef)."_id", ",");


	}








	$dataMap = vanhenry\manager\helpers\DetailTableHelper::getAllDataOfTable($tableMap);


	$arrValue = explode(',', $value);


	$intersectData1 = array_intersect($arrValue, array_keys($dataMapDefault));


	$intersectData2 = array_intersect($arrValue, array_keys($dataMap));


}





?>


<div class="form-group ">


  <p class="form-title" for="">{{FCHelper::ep($table,'note')}}<p/>


  <div class="form-reset">


    <select {{FCHelper::ep($table,'require')==1?'required':''}}  multiple="multiple" style="width:100%" placeholder="{{$table->note}}" class=" {{$isAjax?'ajx_search_multi_'.$name:'select2'}}" name="{{$name}}[]">


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


    		vanhenry\manager\helpers\DetailTableHelper::printOptionRecursiveData($arrData,0,$dataMapDefault,$intersectData1,$intersectData2);


    	?>


    @endif


	</select>


  </div>


  


</div>








@if($isAjax)


<script type="text/javascript">


$(function() {


	$('.ajx_search_multi_{{$name}}').select2({


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