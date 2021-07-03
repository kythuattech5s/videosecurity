

@extends('vh::master')

@section('content')

<div class="header-top aclr">

<div class="breadc pull-left">

  {{-- <i class="fa fa-home pull-left"></i> --}}

  <ul class="aclr pull-left list-link">

    <li class="pull-left"><a href="#">{{$tableData->get('name','')}}</a></li>

  </ul>

</div>


<a class="pull-right bgmain viewsite _vh_save" href="" onclick="calculateCode();$('#frmAssign').submit();">

<i class="fa fa-save" aria-hidden="true"></i>

<span  class="clfff">Lưu</span> 

</a>



</div>

<div id="maincontent">

  <h3>{{\Session::has('errors')?\Session::get("errors"):""}}</h3>

  <form action="{{$admincp}}/do_assign/{{$tableData->get('table_map','')}}" method="post" id="frmAssign">

  {{csrf_field()}}

	<div id="mainedit-permis" class="container-fluid">

    <div class="row m0">

    <div class="form-group">

      <label for="email">Danh sách nhóm người dùng</label>

      <select name="group_user" class="form-control">

          <option value="-1">Không xác định</option>

            @foreach($groupUsers as $gr)

            <option {{$groupUserSelected == $gr->id?'selected':''}} value="{{$gr->id}}">{{$gr->name}}</option>

            @endforeach

      </select>

    </div>

    </div>

    

    <input type="hidden" name="code">

    @foreach($groupModules as $gr)

    <div class="row m0">

      <div class="col-xs-12 bigtitle">

        <p>{{$gr->name}} (<small>{{$gr->note}}</small>)</p>

      </div>

      <?php $subGroupModules =  \vanhenry\manager\model\HGroupModule::where("act",1)->where("parent",$gr->id)->get() ?>

      @foreach($subGroupModules as $sub)

      <div class="col-md-3 groupmodule-item">

          <div class="title clearfix">

            <label class="noselect" >

            <input type="checkbox" value="{{$sub->id}}" class="cball-{{$sub->id}}">

            Chọn tất cả

            </label>

            <span class="sub">{{$sub->name}}</span>

          </div>

          <ul> 

          <?php 

          $modules =\DB::select('select a.* from h_modules a, h_roles b where a.parent = :uid and b.group_user_id= :gid and a.parent = b.group_module_id and (b.role & a.code )>0 group by a.id',['uid'=>$sub->id,"gid"=>$groupUserId]);

           ?>

            @foreach($modules as $module)

            <li><label class="noselect" >

              <input {{in_array($module->id,$arrChecked)?'checked':''}} type="checkbox" value="{{$module->code}}" class="cbitem-{{$module->id}}">

              {{$module->note}}

            </label></li>

            @endforeach

          </ul>

      </div>

      @endforeach

    </div>

    @endforeach

    </form>

  </div>

</div>



<style type="text/css">

  #mainedit-permis{

    background: #fff;

  }

  #mainedit-permis .groupmodule-item {

        background: #efefef;

        margin:5px 0px;

    background-clip: content-box;

  }

  #mainedit-permis .groupmodule-item ul{

    padding:5px;

  }

  #mainedit-permis .groupmodule-item ul li label{

    cursor: pointer;

    width: 100%;

  }

  #mainedit-permis .groupmodule-item ul li{

    cursor:pointer;

    padding:0px 5px;

  }

  #mainedit-permis .groupmodule-item ul li:hover{

    background: #ccc;

  }

  #mainedit-permis .title{

        background:#e96a0c;

    padding-left: 11px;

    color: #fff;

  }

  #mainedit-permis .title .sub{

        font-size: 15px;

    text-transform: uppercase;

    background:#e96a0c;

    margin: 0px;

    padding: 3px 0px;

    color: #fff;

    padding-right: 11px;

    text-align: right;

    float:right;

  }

  #mainedit-permis .bigtitle{

        background: #00923f;

    text-transform: uppercase;

    padding: 5px 15px;

    color: #fff;

  }

  .noselect {

    cursor: pointer;

  -webkit-touch-callout: none; /* iOS Safari */

    -webkit-user-select: none; /* Safari */

     -khtml-user-select: none; /* Konqueror HTML */

       -moz-user-select: none; /* Firefox */

        -ms-user-select: none; /* Internet Explorer/Edge */

            user-select: none; /* Non-prefixed version, currently

                                  supported by Chrome and Opera */

}

</style>

<script type="text/javascript">

  $(function() {

    $("input[type=checkbox][class*=cball]").change(function(event) {

      $(this).parents(".groupmodule-item").find("input[type=checkbox][class*=cbitem]").prop("checked",$(this).is(":checked"));

      

    });

    $("select[name=group_user]").change(function(event) {

      var form ="<form  action = '{{request()->url()}}' method='post'><input type='hidden' name='_token' value='{{csrf_token()}}'/> <input name='group_user' type='hidden' value='"+$(this).val()+"'/></form>";

      $(form).appendTo('body').submit().remove();

    });

  });

  function calculateCode(){

    var listGroups = $(".groupmodule-item");

    var ret =[];

    for (var i = 0; i < listGroups.length; i++) {

      var item = $(listGroups[i]);

      var grid = item.find("input[class*=cball]").val();

      var modules = item.find("input[class*=cbitem]");

      var code = 0;

      for (var j = 0; j < modules.length; j++) {

        var m = $(modules[j]);

        if(m.is(":checked")){

          code += parseInt(m.val());  

        }

      }

      var obj = {};

      obj.code = code;

      obj.groupid = grid;

      ret.push(obj);

    }

    $("input[name=code]").val(JSON.stringify(ret));

  }

</script>

@stop

@section('js')

 <script type="text/javascript" src="{{asset('public/js/manager-menu.js')}}"></script>

@stop