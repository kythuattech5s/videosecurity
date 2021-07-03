<?php 

$name = FCHelper::er($table,'name');

$default_code = FCHelper::er($table,'default_code');

$default_code = json_decode($default_code,true);

$default_code = @$default_code?$default_code:array();

$value ="";

if($actionType=='edit'||$actionType=='copy'){

	$value = FCHelper::er($dataItem,$name);

	}

 ?>

<div class="form-group ">

  <p class="form-title" for="">{{FCHelper::er($table,'note')}}<p/>

  <div class="form-control form-reset flex">

    <select {{FCHelper::ep($table,'require')==1?'required':''}} style="width:100%" placeholder="{{FCHelper::ep($table,'note')}}" class="select2" name="{{$name}}">



    	<?php 

    		vanhenry\manager\helpers\DetailTableHelper::recursiveGroupUser(\Auth::guard("h_users")->id(),$value,0);

    	?>

	</select>

  </div>

  

</div>