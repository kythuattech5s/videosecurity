<?php $tableMap = $tableData->get('table_map',''); ?>
@extends('vh::master')
@section('content')
<div class="header-top aclr">
	<div class="breadc pull-left">
		<ul class="aclr pull-left list-link">
			<li class="pull-left"><a href="#">{{$tableData->get('name','')}}</a></li>
		</ul>
	</div>
	<div>
		{{-- <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">
			<i class="fa fa-external-link" aria-hidden="true"></i>
			<span  class="clfff">{{trans('db::see_website')}}</span> 
		</a>
		<a class="pull-right bgmain viewsite _vh_update" href="#">
			<i class="fa fa-pencil" aria-hidden="true"></i>
			<span  class="clfff">{{trans('db::update')}}</span> 
		</a> --}}
		<a class="pull-right bgmain viewsite _vh_save" href="#">
			<i class="fa fa-save" aria-hidden="true"></i>
			<span  class="clfff">{{trans('db::save')}}</span> 
		</a>
		{{-- <a class="pull-right btn-func tooltipx bottom" href="{{$admincp}}/deleteCache">
			<i class="fa fa-trash-o" aria-hidden="true"></i>
			<span class="tooltiptext">{{trans('db::delete_cache')}}</span>
		</a> --}}
	</div>
</div>

<div id="maincontent">
	<?php 
		$actionAjax = "$admincp/update/".$tableMap."/0";
		$actionNormal = "$admincp/save/".$tableMap."/0?returnurl=".base64_encode(Request::url());  
	?>
	<form action="{{$actionNormal}}" dt-ajax="{{$actionAjax}}" dt-normal="{{$actionNormal}}" method="post" id="frmUpdate">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<ul class="nav nav-tabs config">
			<?php $countRegion = $listRegions->count();  ?>
			@for($i=0;$i< $countRegion;$i++)
				<?php $region = $listRegions[$i]; ?>
				<li class="{{$i==0?'active':''}}"><a data-toggle="tab" href="#menu-{{$i}}">{{FCHelper::ep($region,'name')}}</a></li>
			@endfor
		</ul>
		<div class="tab-content">
			@for($i=0;$i< $countRegion;$i++)
			<?php $region = $listRegions[$i]; ?>
			<div id="menu-{{$i}}" class="tab-pane fade in {{$i==0?'active':''}}">
				@foreach($region->childs as $childRegion)
				<div class="panel panel-default panel-config">
					<div class="panel-heading" id="panel-heading-{{$childRegion->id}}">
						<h4 class="panel-title">{{$childRegion->name}}</h4>
						@if($childRegion->def!=1)
						<a href="#" class="btn bgmain btn-show-config"><i class="fa fa-angle-double-up" aria-hidden="true"></i></a>
						@endif
					</div>
					<div class="panel-body row {{$childRegion->def==1?'show':''}}" style="">
						<?php 
							$idRegion = $childRegion->id;
							$cConfigs = $listConfigs->filter(function ($item) use($idRegion) {
								return $item->region ==$idRegion ;
							});
						?>
						@foreach ($cConfigs as $cKey => $_dataItem)
						<?php 

							$type_show = StringHelper::isNull($_dataItem->type_show)?"base":strtolower($_dataItem->type_show);
							$type_show ="vh::ctedit.".$type_show; 
						?>
						@if(isset($_dataItem->nolang) && $_dataItem->nolang ==1)
						<?php 
							$multilang = array("vi"=>"");
						?>
						@else
						<?php 
							$multilang = $tableData['default_data'];
							$multilang = json_decode($multilang,true);
							$multilang = isset($multilang)?$multilang:array("vi"=>" (Tiếng việt)");
						?>
						@endif
						@foreach($multilang as $lang =>$vlang)
						<?php 
							$table->en_note = $_dataItem->en_note;
							$table->note = $_dataItem->note.$vlang;
							$_key = $lang."_value";
							$_name = $lang."_".strtolower($_dataItem->id);
							$_dataItem->$_name = $_dataItem->$_key;
							$table->name = $_name;
							$table->default_data = $_dataItem->default_data;
							$table->default_code = $_dataItem->default_code;
							$dataItem = (array)$_dataItem;
						?>
						<div class="{{isset($dataItem['width']) && $dataItem['width']==2 ? 'col-sm-6':'col-xs-12' }}">
							@if(View::exists($type_show))
								@include($type_show,array('dataItem'=>$dataItem))
							@endif
						</div>
						@endforeach
						@endforeach
					</div>
				</div>
				@endforeach
			</div>
			@endfor
		</div>
	</form>
	<style type="text/css">
		div[id*='menu'].tab-pane .panel-heading{position:relative;
		}
		.panel-config .panel-heading{background: #fff;
		}
		.panel-config .panel-heading h4{
			text-transform: uppercase;color: #9e9e9e;
		}
		.btn-show-config{color: #fff;position: absolute;right: 3px;top: 2px;
		}
		.btn-show-config:hover,.btn-show-config:active,.btn-show-config:focus,.btn-show-config.active{background: #E96A0C;color:#fff;
		}
		.nav-tabs {border-bottom: 1px solid #00923f;}
		ul.nav li a:hover{    border-bottom: 1px solid;}
		ul.nav li a{text-transform: uppercase;
		}
		ul.nav li.active a{border-radius: 0;text-transform: uppercase;border: 1px solid #00923f !important;border-bottom: 1px solid #fff !important;font-weight: bold;
		}
	</style>
	<script type="text/javascript">
		$(function() {
			var panes = $(".panel-config .panel-heading");
			for (var i = 0; i < panes.length; i++) {
				var item = $(panes[i]);
				var id = item.attr("id");
				var check = localStorage.getItem(id);
				if(check != undefined && check ==1){
					item.next().show();
					item.find(".btn-show-config i").attr("class","fa fa-angle-double-up");
				}
				else{
					item.next().hide();
					item.find(".btn-show-config i").attr("class","fa fa-angle-double-down");
				}
			}
		});
		$(".btn-show-config").click(function(event) {
			event.preventDefault();
			var p = $(this).parents(".panel");
			var b = p.find(".panel-body");
			if(b.is(":hidden")){
				b.slideDown();
				$(this).find("i").attr("class","fa fa-angle-double-up");
				localStorage.setItem(p.find('.panel-heading').attr("id"),1);
			}
			else{
				b.slideUp();
				$(this).find("i").attr("class","fa fa-angle-double-down");
				localStorage.setItem(p.find('.panel-heading').attr("id"),0);
			}
		});
	</script>
	@include('vh::edit.view_edit_script')
</div>
@stop