@extends('vh::master')
@section('content')
<?php $tableMap = $tableData->get('table_map',''); ?>
<input class="one hidden" dt-id="{{FCHelper::er($dataItem,'id')}}" ><!--Lưu id để xóa-->
<div class="header-top aclr">
	<div class="breadc pull-left">
		{{-- <i class="fa fa-comments pull-left"></i> --}}
		<ul class="aclr pull-left list-link">
			<li class="pull-left"><a href="{{$admincp}}/view/{{$tableMap}}">{{$tableData->get('name','')}}</a></li>
		</ul>
		@if($transTable != null)
		<ul class="table-lang edit">
			<?php $tableLangs = \Session::get('_table_lang') ?>
			@foreach($locales as $localeCode => $v)
			<li><a href="{{$admincp}}/table-lang/{{$tableData->get('table_map','')}}/{{$localeCode}}" class="{{!is_array($tableLangs) && $localeCode == Config::get('app.locale_origin') || is_array($tableLangs) && array_key_exists($tableData->get('table_map'), $tableLangs) && $tableLangs[$tableData->get('table_map')] == $localeCode ? 'active' : ''}}">{{$v}}</a></li>
			@endforeach
		</ul>
		@endif
	</div>
	<div>
		@if($actionType=='edit')
		<a class="pull-right bgmain viewsite" href="#">
			<i class="fa fa-copy" aria-hidden="true"></i>
			<span  class="clfff">Nhân bản</span> 
		</a>
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
		<a class="pull-right bgmain1 viewsite" href="{{base64_decode(\Request::input('returnurl'))}}">
			<i class="fa fa-backward" aria-hidden="true"></i>
			<span  class="clfff">Back</span> 
		</a>
	</div>
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
			<div class="col-xs-12 col-md-9 p0">
				<?php 
				$mainTable = count($tableDetailData)>0?$tableDetailData[1]:array(); 
				?>
				@foreach($mainTable as $mTable)
				<?php $currentGroup = count($mTable)>0?$mTable[0]->group:0; ?>
				<?php if(!isset($groupControl[$currentGroup]))
				continue;
				?>
				<div class="row m0 boxedit">
					@if($groupControl[$currentGroup]->has_button_hide==1)
					<div class="col-xs-12 boxtitle">
						<h1 class="col-xs-9 p0">{{FCHelper::ep($groupControl[$currentGroup],'name',1)}}</h1>
						<div class="textright col-xs-3 p0">
							<button type="button" class="btn btn-primary bgmain btnshow">{{trans('db::edit')}}</button>
						</div>
					</div>
					@else
					<h1 class="col-xs-12">{{FCHelper::ep($groupControl[$currentGroup],'name',1)}}</h1>
					@endif
					<p class="des col-xs-12">{{FCHelper::ep($groupControl[$currentGroup],'note',1)}}</p>
					<div class="col-xs-12 {{$groupControl[$currentGroup]->has_button_hide==1?'boxhide':''}} {{$groupControl[$currentGroup]->display_default==0?'none':''}}">
						@foreach($mTable as $table)
						<?php 
						$viewEdit = 'vh::ctedit.'.StringHelper::normal(FCHelper::er($table,'type_show'));
						$viewEdit = View::exists($viewEdit)?$viewEdit:"vh::ctedit.base";
						?>
						@include($viewEdit)
						@endforeach
					</div>
				</div>
				@endforeach
				<?php $exs = \Event::dispatch('vanhenry.manager.insert.generate_view',array($tableData)); ?>
				@foreach ($exs as $exk => $exvs)
					@if(is_array($exvs))
						@foreach($exvs as $exvv)
							@include("vh::".$exvv)
						@endforeach
					@endif
				@endforeach
			</div>
			<div class="col-xs-12 col-md-3">
				<?php 
				$sideTable = count($tableDetailData)>1? $tableDetailData[2]:array(); 
				?>
				@foreach($sideTable as $side)
				<?php $currentGroup = count($side)>0?$side[0]->group:0;?>
				<?php if(!isset($groupControl[$currentGroup]))
				continue;
				?>
				<div class="row m0 boxedit">
					<h1 class="col-xs-12">{{$groupControl[$currentGroup]->name}}</h1>
					<div class="col-xs-12">
						@foreach($side as $table)
						<?php 
						$viewEdit = 'vh::ctedit.'.StringHelper::normal(FCHelper::er($table,'type_show'));
						$viewEdit = View::exists($viewEdit)?$viewEdit:"vh::ctedit.base";
						?>
						@include($viewEdit)
						@endforeach
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</form>
	@if($actionType=='edit')
	<div class="bottom-control row m0">
		<input class="one hidden" dt-id="{{FCHelper::er($dataItem,'id')}}" ><!--Lưu id để xóa-->
		<div class="col-xs-12">
			<a class="pull-left bgmain viewsite _vh_delete" href="{{$admincp}}/delete/{{Request::segment(3)}}">
				<i class="fa fa-trash" aria-hidden="true"></i>
				<span  class="clfff">{{trans('db::delete')}}</span> 
			</a>
			<a class="pull-right bgmain viewsite _vh_update" href="#">
				<i class="fa fa-pencil" aria-hidden="true"></i>
				<span  class="clfff">Cập nhật</span> 
			</a>
		</div>
	</div>
	@endif
	<script type="text/javascript">
		function close_window() {
			parent.$.fancybox.close();
		}
		function hungvtApplyCallbackFile(arrItem,field_id){
			if(arrItem.length==0) return;
			var nxt = $('#'+field_id).prev();
			if($(nxt).prop('tagName').toLowerCase()=='img'){
				var item = arrItem[0];
				var def = $("[name=name]").val();
				if(def!=undefined){
					item.alt = def;
					item.title = def;
					item.caption = def;
					item.description = def;
				}
				$('#'+field_id).val(JSON.stringify(item)).trigger('change');
				$(nxt).attr('src', item.path+item.file_name);
			}
			else{
			}
		}
		function changeListImageV2(_that,inputarget){
			var arr = $(_that).find('img');
			var str =new Array();
			for (var i = 0; i < arr.length; i++) {
				var item = arr[i];
				var tmp = JSON.parse($(item).attr('data-file'));
				str.push(tmp);
			};
			str = JSON.stringify(str);
			$('input[name='+inputarget+']').val(str);
		}
	</script>
	@include('vh::static.footer')
</div>
@stop