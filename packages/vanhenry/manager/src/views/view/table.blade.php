<?php 
	$has_update = $tableData->get('has_update','')==1;
	$has_delete =$tableData->get('has_delete','')==1;
	$has_copy =$tableData->get('has_copy','')==1;
	$has_trash =$tableData->get('has_trash','')==1;
?>
<div class="pagination m0 textcenter show aclr">
	<span class="total inlineblock pull-left">{{trans('db::number_record')}}: <strong>{{$listData->total()}}</strong></span>
	<div class="inlineblock pull-right">
		{{$listData->links()}}
	</div>
</div>
<div id="no-more-tables" class="row m0">
	<div class="tablecontrol none" >
		<a class="_vh_delete_all" href="{{$admincp}}/deleteAll/{{$tableData->get('table_map','')}}" title="{{trans('db::delete_all')}} {{$tableData->get('name','')}}"><i class="fa fa-trash" aria-hidden="true"></i>{{trans('db::delete_all')}}</a>
		@if($tableData->get('table_parent','')!='')
		<a href="#" data-toggle="modal" data-target="#addToParent" class="_vh_add_to_parent" title="Thêm vào danh mục cha"><i class="fa fa-puzzle-piece" aria-hidden="true">Thêm vào danh mục cha</i>
		</a>
		<a href="#" title="Xóa khỏi danh mục cha" data-toggle="modal" data-target="#addToParent" class="_vh_remove_from_parent"><i class="fa fa-chain-broken" aria-hidden="true">Xóa khỏi danh mục cha</i></a>
		@endif
	</div>
	<div class="main_table">
		<table class="col-md-12 table-bordered table-striped table-condensed cf p0 table-data-view">
			<thead class="cf">
				<tr>
					@if($has_delete)
					<th>
						<div class="squaredTwo">
							<input type="checkbox" class="all" value="None" id="squaredTwoall" name="check">
							<label for="squaredTwoall"></label>
						</div>
					</th>
					@endif
					{%FILTER.simpleShow.filterShow.tableDetailData%}
					<th>STT</th>
					@foreach($simpleShow as $show)
					<th>{{$show->note}}</th>
					@endforeach
					<th>Chức năng</th>
				</tr>
			</thead>
			<tbody>
				<?php $urlFull = base64_encode(Request::fullUrl()); ?>
				@for($i= 0;$i<$listData->count();$i++)
				<?php $itemMain = $listData->get($i); ?>
				<tr>
					@if($has_delete)
					<td data-title="#"> 
						<div class="squaredTwo">
							<input type="checkbox" class="one" dt-id ="{{FCHelper::ep($itemMain,'id')}}" id="squaredTwo{{FCHelper::ep($itemMain,'id')}}" name="check">
							<label for="squaredTwo{{FCHelper::ep($itemMain,'id')}}"></label>
						</div>
					</td>
					@endif
					<td data-title="STT">{{$i+1}}</td>
					@foreach($simpleShow as $show)
						<?php 
						$viewView = 'vh::ctview.'.strtolower(FCHelper::er($show,'type_show'));
						$viewView = View::exists($viewView)?$viewView:"vh::ctview.base";
						?>
						@include($viewView,array('item'=>$show,'dataItem'=>$itemMain))
					@endforeach
					<td data-title="{{trans('db::function')}}" style="min-width: 130px;" class="action">
						@isset($itemMain->slug)
						<a href="{{$itemMain->slug}}" target="_blank" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}">
							<i class="fa fa-eye" aria-hidden="true"></i>
							<span class="tooltiptext">Xem demo</span>
						</a>
						@endif
						@if($tableData->get('table_map','') == 'auctions' && new DateTime >= new DateTime($itemMain->start_time))
							@if(new DateTime > new DateTime($itemMain->expired_time))
								<a href="{{$admincp}}/detail/{{$tableData->get('table_map','')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" target="_blank" class="tooltipx">
									<i class="fa fa-eye" aria-hidden="true"></i>
									<span class="tooltiptext">Chi tiết</span>
								</a>
							@else
								<?php $product = App\Models\Auction::find($itemMain->id)->product()->translation()->first(); ?>
								@if($product != null)
								<a target="_blank" href="{{\Support::show($product, 'slug')}}" target="_blank" class="tooltipx">
									<i class="fa fa-eye" aria-hidden="true"></i>
									<span class="tooltiptext">Chi tiết</span>
								</a>
								@endif
							@endif
						@endif
						@if(strpos($tableData->get('table_map'),'order') !== false)
							<?php 
								if($tableData->get('table_map') == "orders"){
									if(isset($itemMain->status)){
										$order_id = $itemMain->id;
									}else{
										$order_id = $itemMain->order_id;
									}
								}elseif(strpos($tableData->get('table_map'),'pre_order') !== false){
									if($tableData->get('table_map') == "pre_orders"){
										$pre_order_id = $itemMain->id;
									}
									else{
										$pre_order_id = $itemMain->pre_order_id;
									}
								}
								else{
									$order_id = $itemMain->order_id;
								}
							 ?>
							@if(is_int(strpos($tableData->get('table_map'),'pre_order')) == false)
								@if(isset($itemMain->status))
								<a href="{{$admincp}}" data-order-id="{{$order_id}}" class="showDetailOrder tooltipx @if($itemMain->status !== 1) sent @endif"><i class="fa fa-eye" aria-hidden="true"></i>
									<span class="tooltiptext">Chi tiết đơn hàng</span>
								</a>
								@else
									<a href="{{$admincp}}" data-order-id="{{$order_id}}" class="showDetailOrder tooltipx @if($itemMain->act == 1) sent @endif"><i class="fa fa-eye" aria-hidden="true"></i>
										<span class="tooltiptext">Chi tiết đơn hàng</span>
									</a>
								@endif
							@else
								<a href="{{$admincp}}" data-pre-order-id="{{$pre_order_id}}" class="showDetailPreOrder tooltipx @if($itemMain->status == 2) sent @endif"><i class="fa fa-eye" aria-hidden="true"></i>
									<span class="tooltiptext">Chi tiết đặt hàng</span>
								</a>
							@endif
						@endif
						@if($has_copy && $tableData->get('table_map','') != 'user_question'  && ($tableData->get('table_map','') != 'auctions' || new DateTime < new DateTime($itemMain->start_time)))
						<a href="{{$admincp}}/copy/{{$tableData->get('table_map','')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}"><i class="fa fa-copy" aria-hidden="true"></i>
							<span class="tooltiptext">Copy</span>
						</a>
						@endif
						@if($has_update)
							@if($tableData->get('table_map','') == 'auctions')
								<!-- @if(new DateTime < new DateTime($itemMain->start_time)) -->
								<a href="{{$admincp}}/edit/{{$tableData->get('table_map','')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}"><i class="fa fa-pencil" aria-hidden="true"></i>
									<span class="tooltiptext">Sửa</span>
								</a>
								<!-- @endif -->
							@else
								<a href="{{$admincp}}/edit/{{$tableData->get('table_map','')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}"><i class="fa fa-pencil" aria-hidden="true"></i>
									<span class="tooltiptext">Sửa</span>
								</a>
							@endif
						@endif
						@if($has_trash)
						<a href="{{$admincp}}/{{isset($trash)?'backtrash':'trash'}}/{{$tableData->get('table_map','')}}" class="_vh_{{isset($trash)?'backtrash':'trash'}} tooltipx {{trans('db::delete')}} {{$tableData->get('table_map','')}}"><i class="fa fa-{{isset($trash)?'level-up':'trash'}}" aria-hidden="true"></i>
							<span class="tooltiptext">{{isset($trash)?'Restore':'Thùng rác'}}</span>
						</a>
						@endif
						@if($has_delete && $tableData->get('table_map','') != 'auctions')
						<a href="{{$admincp}}/delete/{{$tableData->get('table_map','')}}" class="_vh_delete_permanent _vh_delete tooltipx {{trans('db::delete')}} {{$tableData->get('table_map','')}}"><i class="fa fa-times-circle" aria-hidden="true"></i>
							<span class="tooltiptext">Xóa vĩnh viễn</span>
						</a>
						@endif
					</td>
				</tr>
				@endfor
			</tbody>
		</table>
	</div>
	<div class="pagination col-xs-12 m0 textcenter show aclr">
		<span class="total inlineblock pull-left">{{trans('db::number_record')}}:<strong> {{$listData->total()}}</strong></span>
		<div class="inlineblock pull-right">
			{{$listData->links()}}
		</div>
	</div>
</div>