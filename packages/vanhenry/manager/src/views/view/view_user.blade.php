@extends('vh::master')

@section('content')

<a href="{{$admincp}}/editableajax/{{$tableData->get('table_map','')}}" class="hidden" id="editableajax"></a>

<div class="header-top aclr">

  

  <div class="breadc pull-left">

    <i class="fa fa-home pull-left"></i>

    <ul class="aclr pull-left list-link">

      <li class="pull-left"><a href="{{$admincp}}">{{trans('db::home')}}</a></li>

    </ul>

  </div>

  

  <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">

    <i class="fa fa-external-link" aria-hidden="true"></i>

    <span  class="clfff">{{trans('db::see_website')}}</span> 

  </a>





  @if($tableData->get("has_import","")==1)

  <a class="pull-right bgmain viewsite " href="{{$admincp}}/import/{{$tableData->get('table_map','')}}">

    <i class="fa fa-cloud-upload" aria-hidden="true"></i>

    <span  class="clfff">Import</span> 

  </a>

  @endif

  <a class="pull-right btn-func tooltipx bottom" href="{{$admincp}}/deleteCache">

    <i class="fa fa-trash-o" aria-hidden="true"></i>

    <span class="tooltiptext ">{{trans('db::delete_cache')}}</span>

  </a>

</div>

<div id="maincontent">

  <div class="listcontent">

    <ul class="nav nav-tabs">

      <li class="active"><a  href="{{$admincp}}/view/{{$tableData->get('table_map','')}}">

      {{trans('db::allof')}} {{FCHelper::ep($tableData,'name')}}</a></li>

    </ul>



    <div class="tab-content">

      <div id="home" class="tab-pane fade in active">

        <div class="filter aclr">

          <form id="frmsearch" method="post" action="{{$admincp}}/search/{{$tableData->get('table_map','')}}" class="">

            {{csrf_field()}}

            <div class="form">

            </form>

          </div>

          

          <div id="main-table">

            <?php 

            $has_update = $tableData->get('has_update','')==1;

            $has_delete =$tableData->get('has_delete','')==1;

            $has_copy =$tableData->get('has_copy','')==1;

            $has_trash =$tableData->get('has_trash','')==1;

            ?>

            <div class="pagination m0 textcenter show aclr">

              <span class="total inlineblock pull-left">{{trans('db::number_record')}}: <strong>{{$listData->total()}}</strong></span>

              <div class="inlineblock pull-right">

                {%PAGINATION.listData%}

              </div>

            </div>

            <div id="no-more-tables" class="row m0">

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

                <tr>

                  <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Tổng số đơn hàng:</td>

                  <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{{$listData->total()}}</td>

                </tr>

                <tr>

                  <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Số đơn hàng đã hoàn thành:</td>

                  <td style="    border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{{\DB::table("total_orders")->where("status",7)->count()}}</td>

                </tr>

              </table>



              <table class="col-md-12 table-bordered table-striped table-condensed cf p0 table-data-view">

                <thead class="cf">

                  <tr>

                    <th>

                      <div class="squaredTwo">

                        <input type="checkbox" class="all" value="None" id="squaredTwoall" name="check">

                        <label for="squaredTwoall"></label>

                      </div>

                    </th>



                    {%FILTER.simpleShow.filterShow.tableDetailData%}

                    <th>STT</th>

                    @foreach($simpleShow as $show)

                    @if(FCHelper::er($show,'type_show',1)!='PRIMARY_KEY')

                    <th>{{$show->note}}</th>

                    @endif

                    @endforeach

                    <th>Chức năng</th>

                  </tr>

                </thead>

                <tbody>

                  <?php $urlFull = base64_encode(Request::fullUrl()); ?>

                  @for($i= 0;$i<$listData->count();$i++)

                  <?php $itemMain = $listData->get($i); ?>



                  <tr>

                    <td data-title="#"> 

                      <div class="squaredTwo">

                        <input type="checkbox" class="one" dt-id ="{{FCHelper::ep($itemMain,'id')}}" id="squaredTwo{{FCHelper::ep($itemMain,'id')}}" name="check">

                        <label for="squaredTwo{{FCHelper::ep($itemMain,'id')}}"></label>

                      </div>

                    </td>

                    <td data-title="STT">{{$i+1}}</td>

                    @foreach($simpleShow as $show)

                    <?php 

                    $viewView = 'vh::ctview.'.strtolower(FCHelper::er($show,'type_show'));

                    $viewView = View::exists($viewView)?$viewView:"vh::ctview.base";

                    ?>

                    @include($viewView,array('item'=>$show,'dataItem'=>$itemMain))



                    @endforeach

                    <td data-title="{{trans('db::function')}}" style="min-width: 130px;" class="action">

                      <a href="{{$admincp}}/edit/{{$itemMain->type=='1'?'order_pcbs':($itemMain->type=='2'?'order_stencils':'orders')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}"><i class="fa fa-eye" aria-hidden="true"></i>

                        <span class="tooltiptext">Xem</span>

                      </a>

                    </td>

                  </tr>

                  @endfor

                </tbody>

              </table>

              <div class="pagination col-xs-12 m0 textcenter show aclr">

                <span class="total inlineblock pull-left">{{trans('db::number_record')}}:<strong> {{$listData->total()}}</strong></span>

                <div class="inlineblock pull-right">

                  {%PAGINATION.listData%}

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

    @include('vh::static.footer')

  </div>

  @stop

  @section('more')

  @stop