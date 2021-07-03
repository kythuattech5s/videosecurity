<?php 
$name = FCHelper::er($table,'name');
$value ="";

 ?>
<input  {{FCHelper::ep($table,'require')==1?'required':''}} type="text" name="{{$name}}" placeholder="{{FCHelper::er($table,'note')}}"  dt-type="{{FCHelper::er($table,'type_show')}}" value="{{$value}}" />