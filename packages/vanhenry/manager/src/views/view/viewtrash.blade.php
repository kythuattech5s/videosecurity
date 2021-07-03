@extends('vh::master')

@section('content')

<a href="{{$admincp}}/editableajax/{{$tableData->get('table_map','')}}" class="hidden" id="editableajax"></a>

<div class="header-top aclr">

  

  <div class="breadc pull-left">

    <i class="fa fa-home pull-left"></i>

    <ul class="aclr pull-left list-link">

      <li class="pull-left"><a href="{{$admincp}}">Trang chủ</a></li>

    </ul>

  </div>

  

  <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">

    <i class="fa fa-external-link" aria-hidden="true"></i>

    <span  class="clfff">Xem website</span> 

  </a>

  @if($tableData->get('has_insert','')==1)

  <?php $urlFull = base64_encode(Request::fullUrl()); ?>

  <a class="pull-right bgmain viewsite " href="{{$admincp}}/insert/{{$tableData->get('table_map','')}}?returnurl={{$urlFull}}">

    <i class="fa fa-file-o" aria-hidden="true"></i>

    <span  class="clfff">Thêm mới</span> 

  </a>

  @endif

  @if($tableData->get("has_import","")==1)

    <a class="pull-right bgmain viewsite " href="{{$admincp}}/import/{{$tableData->get('table_map','')}}">

      <i class="fa fa-cloud-upload" aria-hidden="true"></i>

      <span  class="clfff">Import</span> 

    </a>

  @endif

</div>

<div id="maincontent">

    <div class="listcontent">

      <ul class="nav nav-tabs">

        <li class=""><a  href="{{$admincp}}/view/{{$tableData->get('table_map','')}}">{{trans('db::allof')}} {{$tableData->get('name','')}}</a></li>

        @if($tableData->get("has_trash","")==1)

        <li class="active"><a  href="{{$admincp}}/trashview/{{$tableData->get('table_map','')}}">Trash</a></li>

        @endif

      </ul>

      

      <div class="tab-content">

        <div id="home" class="tab-pane fade in active">

          <div class="filter aclr">

            

              <div class="advancefilter pull-left">

                <button type="button" class="robo  clmain btnfilter">{{trans('db::CONDITION_FILTER')}}<span class="caret"></span></button>

                <div class="row setfilter">

                <h3>{{trans('db::show')}} {{$tableData->get('name','')}} {{trans('db::as')}} </h3>

                  

                  {%FILTER.advanceSearchs.filterAdvanceSearch.tableDetailData%}

                  <select name="keychoose" class="select2" style="width:100%">

                    <option value="-1">{{trans('db::choose_condition_filter')}}</option>

                    @foreach(@$advanceSearchs as $c)

                    <option dt-type="{{$c->type_show}}" value="{{$c->name}}">{{$c->note}}</option>

                    @endforeach

                  </select>

                  <span class="show">là</span>

                  <div class="add">

                    @foreach(@$advanceSearchs as $c)

                    <?php 

                      $viewSearch = 'vh::search.'.strtolower(FCHelper::er($c,'type_show'));

                      $viewSearch = View::exists($viewSearch)?$viewSearch:"vh::search.text";

                    ?>

                      @include($viewSearch,array('item'=>$c))

                    @endforeach

                  </div>

                  <button type="button" class="btnadd">{{trans('db::add_condition_filter')}}</button>

                  <button type="button" class="btnclose">{{trans('db::close')}}</button>

                </div>

              </div>

              <form method="post" action="{{$admincp}}/search/{{$tableData->get('table_map','')}}" class="ajxform_simple">

              <div class="form">

              <div class="boxsearch">

                <i class="fa fa-search"></i>

                <input type="hidden" name="trash" value="1">

                {%FILTER.simpleSearch.filterSimpleSearch.tableDetailData%}

                <input type="text" name="raw_{{$simpleSearch->name}}" placeholder="{{trans('db::search')}} {{trans('db::as')}} {{$simpleSearch->note}}" value="{{@$dataSearch?FCHelper::er($dataSearch,'raw_'.$simpleSearch->name,1):''}}">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

              </div>

              <button type="submit">{{trans('db::search')}}</button>

              </div>

            

            <div class="listfilter">

              <ul class="aclr">

              </ul>

            </div>

            <div class="orderby aclr">

                <div class="pull-left">

                  <h4 class="pull-left">{{trans('db::orderby')}} </h4>

                  <select name="orderkey" class="select2 pull-left">

                    

                    {%FILTER.simpleSort.filterSimpleSort.tableDetailData%}



                    @foreach($simpleSort as $ss)

                    <option {{$ss->type_show == "PRIMARY_KEY"?"selected":""}} value="{{$ss->name}}">{{$ss->note}}</option>

                    @endforeach

                  </select>

                  <select name="ordervalue" class="select2 pull-left">

                    <option value="asc">{{trans('db::from')}} A->Z</option>

                    <option selected value="desc">{{trans('db::from')}} Z->A</option>

                  </select>

                </div>

                <div class="pull-left">

                  <h4 class="pull-left">{{trans('db::show')}}</h4>

                  <select name="limit" class="select2 pull-left">

                    <option value="10">10</option>

                    <option value="20">20</option>

                    <option value="50">50</option>

                    <option value="100">100</option>

                  </select>

                </div>

            </div>

            </form>

          </div>

          

          <div id="main-table">

            @include('vh::view.table',['tableData'=>$tableData,"trash"=>1])

          </div>

      







        </div>

      </div>

    </div>

    @include('vh::static.footer')

</div>



@stop

@section('more')

  @if($tableData->get('table_parent','')!='')

    @include('vh::view.addToParent')

  @endif

@stop