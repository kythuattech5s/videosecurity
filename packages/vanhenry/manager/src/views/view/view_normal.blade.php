@extends('vh::master')
@section('content')
<a href="{{$admincp}}/editableajax/{{$tableData->get('table_map','')}}" class="hidden" id="editableajax"></a>
<div class="header-top aclr">
	<div class="breadc pull-left">
		<ul class="aclr pull-left list-link">
			<li class="active"><a  href="{{$admincp}}/view/{{$tableData->get('table_map','')}}">{{FCHelper::ep($tableData,'name')}}</a></li>
		</ul>
	</div>


	@if($tableData->get("has_export","")==1)
	<div class="breadc pull-right">
		<a href="{{$admincp}}/export/{{$tableData->get('table_map','')}}">Xuất file excel <i class="fa fa-file-excel-o" aria-hidden="true" style="font-size: 20px; padding-left: 10px;"></i>
		</a>
	</div>
	@endif
</div>
<div id="maincontent">
	<div class="listcontent">
		<ul class="nav nav-tabs">
			@if($tableData->get("has_trash","")==1)
				<li class=""><a  href="{{$admincp}}/trashview/{{$tableData->get('table_map','')}}">{{trans('db::trash')}}</a></li>
			@endif
			@if($transTable != null)
				<li>
					<ul class="table-lang view">
						<?php $tableLangs = \Session::get('_table_lang') ?>
						@foreach($locales as $localeCode => $v)
						<li><a href="{{$admincp}}/table-lang/{{$tableData->get('table_map','')}}/{{$localeCode}}" class="{{(isset($tableLangs[$tableData->get('table_map')]) && $tableLangs[$tableData->get('table_map')] == $localeCode) || (!isset($tableLangs[$tableData->get('table_map')]) && $localeCode == Config::get('app.locale_origin')) ? 'active' : ''}}">{{$v}}</a></li>
						@endforeach
					</ul>
				</li>
			@endif
			<div class="header-top aclr">
				{{--
				<div class="breadc pull-left">
					<ul class="aclr pull-left list-link">
						<li class="pull-left"><a href="{{$admincp}}">{{trans('db::home')}}</a></li>
					</ul>
					<?php $exs = \Event::dispatch('vanhenry.manager.headertop.view',[]); ?>
					@foreach ($exs as $exk => $exvs)
					@if(is_array($exvs))
					@foreach($exvs as $exvv)
					@include($exvv)
					@endforeach
					@endif
					@endforeach
				</div>
				--}}
				<div>
					{{-- <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">
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
					@if($tableData->get('table_map', '') == 'users' || $tableData->get('table_map', '') == 'register_events')
					<button type="button" class="btn btn-success pull-right btnexcel" style="right: 0px;position: relative;" rel="{{$admincp}}/export/{{$tableData->get('table_map','')}}">Export to file Excel</button>
					@endif --}}
					@if($tableData->get('has_insert','')==1 && is_int(strpos($tableData->get('table_map'),'order')) == false)
						<?php $urlFull = base64_encode(Request::fullUrl()); ?>
						<a class="pull-right bgmain viewsite " href="{{$admincp}}/insert/{{$tableData->get('table_map','')}}?returnurl={{$urlFull}}">
							<i class="fa fa-file-o" aria-hidden="true"></i>
							<span  class="clfff">{{trans('db::add')}}</span> 
						</a>
					@endif
				</div>
			</div>
		</ul>
		<div class="tab-content">
			<div id="home" class="tab-pane fade in active">
				<div class="filter aclr">
					<div class="advancefilter pull-left">
						<button type="button" class="robo  clmain btnfilter">{{trans('db::condition_filter')}}<span class="caret"></span></button>
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
								$viewSearch2 = View::exists($viewSearch)?$viewSearch:"vh::search.text";
								?>
								@include($viewSearch2,array('item'=>$c))
								@endforeach
							</div>
							<button type="button" class="btnadd">{{trans('db::add_condition_filter')}}</button>
							<button type="button" class="btnclose">{{trans('db::close')}}</button>
						</div>
					</div>
					<form id="frmsearch" action="{{$admincp}}/search/{{$tableData->get('table_map','')}}" class="">

						<div class="form">
							<div class="boxsearch">
								<i class="fa fa-search"></i>
								{%FILTER.simpleSearch.filterSimpleSearch.tableDetailData%}
								@if($simpleSearch !== null)
								<input type="text" name="raw_{{$simpleSearch->name}}" placeholder="{{trans('db::search')}} {{trans('db::as')}} {{FCHelper::ep($simpleSearch,'note')}}" value="{{@$dataSearch?FCHelper::er($dataSearch,'raw_'.$simpleSearch->name,1):''}}">
								@endif
							</div>
							<button type="submit">{{trans('db::search')}}</button>
						</div>
						<div class="listfilter">
							<ul class="aclr">
								@if(isset($dataSearch))
									@foreach($dataSearch as $k => $v)
										@if(Str::startsWith($k, 'text-'))
											{!!$v!!}
										@endif
									@endforeach
								@endif
							</ul>
						</div>
						<div class="orderby aclr">
							<div class="pull-left">
								<h4 class="pull-left">{{trans('db::orderby')}} </h4>
								<select name="orderkey" class="select2 pull-left">
									{%FILTER.simpleSort.filterSimpleSort.tableDetailData%}
									@foreach($simpleSort as $ss)
									@if(!isset($dataSearch) || $dataSearch['orderkey'] == 'id')
									<option {{$ss->type_show == "PRIMARY_KEY"?"selected":""}} value="{{$ss->name}}">{{$ss->note}}</option>
									@else
									<option {{$ss->name == $dataSearch['orderkey']?"selected":""}} value="{{$ss->name}}">{{$ss->note}}</option>
									@endif
									@endforeach
								</select>
								<select name="ordervalue" class="select2 pull-left">
									@if(isset($dataSearch['ordervalue']))
									<option {{$dataSearch['ordervalue'] == 'desc' ? 'selected' : ''}} value="desc">{{trans('db::from')}} Z->A</option>
									<option {{$dataSearch['ordervalue'] == 'asc' ? 'selected' : ''}} value="asc">{{trans('db::from')}} A->Z</option>
									@else
									<option selected value="desc">{{trans('db::from')}} Z->A</option>
									<option value="asc">{{trans('db::from')}} A->Z</option>
									@endif
								</select>
							</div>
							<div class="pull-left">
								<h4 class="pull-left">{{trans('db::show')}}</h4>
								<select name="limit" class="select2 pull-left">
									<option {{isset($dataSearch) && $dataSearch['limit'] == 10 ? 'selected' : ''}} value="10">10</option>
									<option {{isset($dataSearch) && $dataSearch['limit'] == 20 ? 'selected' : ''}} value="20">20</option>
									<option {{isset($dataSearch) && $dataSearch['limit'] == 50 ? 'selected' : ''}} value="50">50</option>
									<option {{isset($dataSearch) && $dataSearch['limit'] == 100 ? 'selected' : ''}} value="100">100</option>
								</select>
							</div>
							{!!$dataReuse ?? ''!!}
						</div>
					</form>
				</div>
				<div id="main-table">
					@include('vh::view.table',['tableData'=>$tableData])
				</div>
			</div>
		</div>
	</div>
	@if(strpos($tableData->get('table_map'),'order') !== false)
		<div class="modalOrder">
		</div>
		<script src="theme/frontend/js/moment.min.js" defer></script>
		<link rel="stylesheet" href="theme/frontend/css/daterangepicker.css">
		<script src="theme/frontend/js/daterangepicker.js" defer=""></script>
		<script type="text/javascript" src="admin/js/order.js" defer></script>
	@endif
	@include('vh::static.footer')
</div>
@stop
@section('more')
@if($tableData->get('table_parent','')!='')
@include('vh::view.addToParent')
@endif
@stop