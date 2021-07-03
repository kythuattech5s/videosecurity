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

  <table style="border-collapse: collapse;border: 1px solid #ccc;width: 100%;">
    <tr>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Họ tên:</td>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{{$cuser->name}}</td>
    </tr>
    <tr>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Số điện thoại:</td>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;"><a href="tel:{{$cuser->phone}}">{{$cuser->phone}}</a></td>
    </tr>
    <tr>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Địa chỉ:</td>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{{$cuser->address}}</td>
    </tr>
    <tr>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Email:</td>
      <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;"><a href="mailto:{{$cuser->email}}"> {{$cuser->email}}</a></td>
    </tr>
  </table>


  @endif

</div>

@endif

