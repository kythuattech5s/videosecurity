@extends('vh::master')
@section('content')
<div class="header-top aclr">
  {{-- <div class="breadc pull-left">
    <i class="fa fa-home pull-left"></i>
    <ul class="aclr pull-left list-link">
      <li class="pull-left"><a href="{{$admincp}}">Trang chủ</a></li>
    </ul>
  </div> --}}
  
  {{-- <a class="pull-right bgmain1 viewsite"  target="_blank" href="">
    <i class="fa fa-external-link" aria-hidden="true"></i>
    <span  class="clfff">Xem website</span> 
  </a> --}}
 
</div>
<div id="maincontent" style="position: relative;">
    <div class="listcontent">
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#home">Media Manager</a></li>
      </ul>
      <iframe  src="{{$admincp}}/media/view" style="width: 100%;height:100%;position: absolute;top: 0;bottom: 0;right: 0;left: 0;" frameborder="0"></iframe>
      
    </div>
    @include('vh::static.footer')
</div>

@stop