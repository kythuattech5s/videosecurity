<?php 
$tableName = $tableData->get('name','');
 ?>
@extends('vh::master')
@section('content')
<a href="{{$admincp}}/editableajax/{{$tableData->get('table_map','')}}" class="hidden" id="editableajax"></a>
<div class="header-top aclr">
  <button class="nav-trigger pull-left" ></button>
  <div class="breadc pull-left">
    <i class="fa fa-home pull-left"></i>
    <ul class="aclr pull-left list-link">
      <li class="pull-left"><a href="#">{{$tableName}}</a></li>
    </ul>
  </div>
  
  <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">
    <i class="fa fa-external-link" aria-hidden="true"></i>
    <span  class="clfff">Xem website</span> 
  </a>

</div>
<div id="maincontent">
    <div class="menu-manage">
    <div class="row">
      <div class="col-md-3 col-xs-12">
        <div class="menu-manage-l">
          <h4>{{trans('db::LIST')}} menu</h4>
          <p>{{$tableData->get('note','')}}</p>              
        </div>
      </div>
      <div class="col-md-9">
          <?php $count = count($groupMenus); ?>
          @for($i=0;$i<$count;$i++)
          <?php $gmenu= $groupMenus[$i]; ?>
          @if($i==0|| $i%2==0)
          <div class="row">
          @endif
          <?php 

            $_menu = $menus->filter(function($item) use($gmenu){
              return $item->group_id == $gmenu->id;
            });
           ?>
          @if($_menu->count()>0)
          <div class="col-md-6 col-xs-12">
            <div class="menu-box">
              <div class="menu-box-head">
                <a href="#" title="" class="collapse-btn open">{{trans('db::SHOWALL')}}</a>
                <a href="{{$admincp}}/edit/{{$tableData->get('table_parent','')}}/{{FCHelper::er($_menu->get(0),'id')}}" title="">{{trans('db::edit')}} {{$tableData->get('name','')}}</a>
                <h3>{{$gmenu->name}}</h3>
              </div>
              <div class="menu-box-ct">
                <?php \vanhenry\manager\GlobalHelper::printMenu($admincp,$tableData,$_menu) ?>
              </div>
            </div>
          </div>
          @endif
          @if($i==$count-1|| $i%2==1)
          </div>
          @endif
          @endfor
        
      </div>
    </div>
    </div>
    @include('vh::static.footer')
</div>

@stop
