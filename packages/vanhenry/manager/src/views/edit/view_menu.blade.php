<?php $tableMap = $tableData->get('table_map','');
$name = $tableData->get('name','');
if($actionType=='edit'){
  $actionAjax = "$admincp/update/".$tableMap."/".FCHelper::er($dataItem,'id');
  $actionNormal = "$admincp/save/".$tableMap."/".FCHelper::er($dataItem,'id')."?returnurl=".Request::input('returnurl');  
}
  $default_data = $tableData->get('default_data','');
  $default_data = json_decode($default_data,true);
  $showInMenu = $default_data['showinmenu'];
?>
@extends('vh::master')
@section('content')
<div class="header-top aclr">
{{-- <button class="nav-trigger pull-left" ></button> --}}
<div class="breadc pull-left">
  {{-- <i class="fa fa-home pull-left"></i> --}}
  <ul class="aclr pull-left list-link">
    <li class="pull-left"><a href="#">{{$tableData->get('name','')}}</a></li>
  </ul>
</div>
  {{-- <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">
    <i class="fa fa-external-link" aria-hidden="true"></i>
    <span  class="clfff">{{trans('db::see_website')}}</span> 
  </a>
  <a class="pull-right bgmain viewsite _vh_update" href="{{$actionAjax}}" onclick="_VH_MENU_MAIN.submitMenu($(this).attr('href'),false);return false;">
  <i class="fa fa-pencil" aria-hidden="true"></i>
  <span  class="clfff">{{trans('db::update')}}</span> 
  </a>
  <a class="pull-right bgmain viewsite _vh_save" href="{{$actionNormal}}" onclick="_VH_MENU_MAIN.submitMenu($(this).attr('href'),true);return false;">
  <i class="fa fa-save" aria-hidden="true"></i>
  <span  class="clfff">{{trans('db::save')}}</span> 
  </a> --}}
</div>
<div id="maincontent">
	<form id="formMenu">
      <div class="menu-manage">
        <div class="row">
           <div class="col-md-3 col-xs-12">
            <div class="menu-manage-l">
              <h4>Thông tin menu</h4>
              <p>Nhập tên của menu.</p>              
            </div>
          </div>
          <div class="col-md-9 col-xs-12">
            <div class="menu-manage-r">
                <div class="form-group">
                  <label class="control-label">Tên menu</label>
                  <input type="text" class="form-control" value="{{$dataItem->name}}" placeholder="Vd: example.vn">
                </div>
                <div class="form-group">
                  <label class="control-label">Đường dẫn</label>
                  <input type="text" value="{{$dataItem->link}}" class="form-control">
                </div>
            </div>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-3 col-xs-12">
            <div class="menu-manage-l">
              <h4>Liên kết</h4>
              <p>Kéo thả liên kết để thay đổi thứ tự hiển thị.</p>
              <button id="add-menu" type="button" class="btn">Thêm liên kết</button>
            </div>
          </div>
          <div class="col-md-9 col-xs-12">
          <ul class="nav-list first">
              <li class="nav-item">
                <div class="nav-line">
                  <span class="arrow fa fa-arrows"></span>
                  <button type="button" class="plus"><i class="fa fa-plus-circle"></i></button>
                  @foreach($tableDetailData as $dt)
                    <?php 
                      $viewEdit = 'vh::ctedit.menu.'.StringHelper::normal(FCHelper::er($dt,'type_show'));
                      $viewEdit = View::exists($viewEdit)?$viewEdit:"vh::ctedit.base";
                    ?>
                    @include($viewEdit,array('table'=>$dt))
                  @endforeach
                  @include("vh::edit.menu.menu_select")
                  
                  <button type="button" class="del-menu btn btn-danger"><i class="fa fa-trash-o"></i></button>
                </div>
                <!-- <ul class="nav-list"></ul> -->
              </li>
            </ul>
          
            <div class="no-menu-table text-center">
              <h4>Menu này hiện chưa có liên kết nào</h4>
              <p>Ấn vào nút bên trái để thêm liên kết</p>
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
          </div>
        </div>
      </div>
    </form>
     @include('vh::static.footer')
</div>
<style type="text/css">
  .tablemain .form-group{
    margin:0;
  }
</style>
@stop
@section('js')
<link rel="stylesheet" type="text/css" href="{{asset('public/menu/css/style.css')}}">
 <script type="text/javascript" src="{{asset('public/menu/js/script.js')}}"></script>
@stop