<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code?$default_code:array();
$value = 0;
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
	}
 ?>
 @if($value>0)
<div class="form-group {{FCHelper::er($table,'hide')==1?'hidden':''}}">
  <p class="form-title" for="">{{FCHelper::er($table,'note')}} <span class="count"></span></p>
  <?php $cuser = \App\User::find($value); ?>
  @if($cuser!=null)

  <p>Họ tên: {{$cuser->name}}</p>
  <p>Số điện thoại: {{$cuser->phone}}</p>
  <p>Địa chỉ: {{$cuser->address}}</p>
  <p>Email: {{$cuser->email}}</p>
  @endif
</div>
@endif
