@extends('vh::master')

@section('content')

<?php $tableMap = $tableData->get('table_map',''); ?>

<input class="one hidden" dt-id="{{FCHelper::er($dataItem,'id')}}" ><!--Lưu id để xóa-->

<div class="header-top aclr">

  <button class="nav-trigger pull-left" ></button>

  <div class="breadc pull-left">

    <i class="fa fa-comments pull-left"></i>

    <ul class="aclr pull-left list-link">

      <li class="pull-left"><a href="{{$admincp}}/view/{{$tableMap}}">{{$tableData->get('name','')}}</a></li>

    </ul>

  </div>

  <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">

    <i class="fa fa-external-link" aria-hidden="true"></i>

    <span  class="clfff">Xem website</span> 

  </a>

  @if($actionType=='edit')

  <a class="pull-right bgmain viewsite _vh_save" href="#">

    <i class="fa fa-save" aria-hidden="true"></i>

    <span  class="clfff">Lưu</span> 

  </a>

  <a class="pull-right bgmain viewsite _vh_delete" href="{{$admincp}}/delete/{{$tableMap}}">

    <i class="fa fa-trash" aria-hidden="true"></i>

    <span  class="clfff">{{trans('db::delete')}}</span> 

  </a>

  @else

  <a class="pull-right bgmain viewsite _vh_save" href="#">

    <i class="fa fa-save" aria-hidden="true"></i>

    <span  class="clfff">Lưu</span> 

  </a>

  @endif

</div>

<?php 

if($actionType=='edit'){

  $actionAjax = "$admincp/update/".$tableMap."/".FCHelper::er($dataItem,'id');

  $actionNormal = "$admincp/save/".$tableMap."/".FCHelper::er($dataItem,'id')."?returnurl=".Request::input('returnurl');  

}

else{

  $actionAjax = "$admincp/storeAjax/".$tableMap;

  $actionNormal = "$admincp/store/".$tableMap."?returnurl=".Request::input('returnurl'); 

}

?>

<div id="maincontent">

  <form action="{{$actionNormal}}" dt-ajax="{{$actionAjax}}" dt-normal="{{$actionNormal}}" method="post" id="frmUpdate">

    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <input type="hidden" name="tech5s_controller" value="{{$tableData->get('controller','')}}">

    <div id="mainedit" class="row">

      <div class="col-xs-12 bgfff p0">

        <style type="text/css">

  table#order_pcb{

        width: 100%;

    border-collapse: collapse;

    border: 1px solid #ccc;

  }

  table#order_pcb tr, table#order_pcb td{

    border-collapse: collapse;

    border: 1px solid #ccc;

    padding: 10px;

  }

</style>

<h3 style="    text-align: center;">THÔNG TIN USER</h3>

<?php $cuser = \App\User::find($dataItem->user_id); ?>



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

  <h3 style="    text-align: center;">THÔNG TIN ĐƠN HÀNG</h3>

<table id="order_pcb">

  <tbody>

    <tr>

      <td><strong>M&atilde; đơn h&agrave;ng</strong></td>

      <td>STENCIL-{{$dataItem->id}}</td>

      <td><strong>Gerber file</strong></td>

      <td colspan="3"><a style="    color: #68bc71;" href="{{$dataItem->file}}" target="_blank">{{basename($dataItem->file)}}</a></td>

    </tr>

    <tr>

      <td><strong>Loại board</strong></td>

      <td>{{HaviHelper::$STELCIL_TYPES[$dataItem->type][1]}}</td>

      <td><strong>Phản hồi</strong></td>

      <td colspan="3"><textarea name="feedback" style="width: 100%;height: 150px;">{{$dataItem->feedback}}</textarea></td>

    </tr>

    <tr>

      <td><strong>K&iacute;ch thước</strong></td>

      <td>{{HaviHelper::$STELCIL_SIZES[$dataItem->size][1]}}</td>

      <td><strong>Số lượng</strong></td>

      <td><i>{{$dataItem->qty}}</i></td>

      <td><strong>Độ dày</strong></td>

      <td>{{HaviHelper::$STELCIL_THICHNESS[$dataItem->thichness][1]}}</td>

    </tr>

    <tr>

      <td>&nbsp;</td>

      <td>&nbsp;</td>

      <td><Strong>Trạng thái</Strong></td>

      <td> <select name="status" class="w100">

          <?php $statuses = \App\Status::where("act",1)->orderBy("ord","asc")->get() ?>

          @foreach($statuses as $status)

          <option {{$status->id==$dataItem->status?'selected':''}} value="{{$status->id}}">{{$status->name}}</option>

          @endforeach

        </select></td>

      <td><strong>Ng&agrave;y tạo</strong></td>

      <td>{{$dataItem->created_at}}</td>

    </tr>

    <tr>

      <td><strong>Ng&agrave;y cập nhật</strong></td>

      <td>{{$dataItem->updated_at}}</td>

<td><strong>Thời gian gia c&ocirc;ng</strong></td>

      <td><input class="w100" type="text" name="time_make" value="{{$dataItem->time_make}}"></td>

      <td><strong>Dự kiến giao h&agrave;ng</strong></td>

      <td>

        <?php 

            if(isset($dataItem->time_expect)){

              $time_expect = \DateTime::createFromFormat('Y-m-d H:i:s', $dataItem->time_expect)->format('d/m/Y H:i:s');

            }

            else{

              $time_expect = (new \DateTime())->format('d/m/Y H:i:s');

            }

          

          ?>

        <input value="{{$time_expect}}" class="w100 datepicker" type="text">

        <input value="{{$dataItem->time_expect}}" name="time_expect"  dt-type="DATETIME" type="hidden">

      </td>

    </tr>

    <tr>

      <td><strong>Y&ecirc;u cầu kh&aacute;c</strong></td>

      <td colspan="5"><strong><i>{{$dataItem->content}}</i></strong></td>

    </tr>

    <tr>

      <td colspan="5"><strong>Tổng tiền</strong></td>

      <td><strong style="color:#d29925"><input type="text" class="total_money" value="{{number_format($dataItem->total_money)}}">
        <input class="hidden" type="number" name="total_money" value="{{$dataItem->total_money}}"> đ</strong></td>

    </tr>

  </tbody>

</table>

      </div>

    </div>

</form>
<script type="text/javascript">
  $(function() {
    $(document).on("input","input.total_money",function(){
      var val = $(this).val();
      val = parseFloat(val.replace(/,/g, ""))
      .toString()
      .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      $(this).val(val);
      $("input[name=total_money]").val(parseFloat(val.replace(/,/g, "")).toString());
    });
  });
</script>


@include('vh::static.footer')

</div>



@stop