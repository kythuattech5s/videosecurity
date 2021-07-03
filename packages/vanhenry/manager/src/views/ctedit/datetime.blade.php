<?php 
$name = FCHelper::er($table,'name');
$valueShow =date("d/m/Y H:i:s");
$valueHidden = date("Y-m-d H:i:s");
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::ep($dataItem,$name);
  if($value==""){
    // $valueShow = date("d/m/Y H:i:s");
    // $valueHidden = date("Y-m-d H:i:s");
  }
  else{
    if( strtotime($value) <0 ){
    }
    else{
      $valueShow = \DateTime::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i:s');
      $valueHidden = $value;  
    }
    
  }
	
}
 ?>
<div class="form-group ">
  <p class="form-title" for="">{{FCHelper::ep($table,'note',1)}}<p/>
  <div class="form-control flex">
    <i class="fa fa-calendar"></i>
    <input value="{{$valueShow}}"   dt-type="{{FCHelper::er($table,'type_show')}}" class="date-control datepicker" type="text" />
    <input value="{{$valueHidden}}"  placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}}  type="hidden" name="{{$name}}">
  </div>
</div>
<script type="text/javascript">
	$(function() {
		$.datetimepicker.setLocale('{{App::getLocale()}}');
	});
</script>