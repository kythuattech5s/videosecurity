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
				<th>Khung giờ</th>
				<th>Sản phẩm</th>
				<th>Lượt người mua đặt nhắc nhở</th>
				<th>Lượt nhấp chuột/ xem</th>
				<th>Bật/ tắt</th>
				<th>Thao tác</th>
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
                    <td>
                         <p>
                              {{date('H:i d-m-Y',strtotime($itemMain->start_at))}}
                         </p>
                         <p>
                              {{date('H:i d-m-Y',strtotime($itemMain->expired_at))}}
                         </p>
                    </td>
                    <td>
                         @foreach($products[$i] as $item)
                              <a href="{{route('product',$item->slug)}}" target="_blank">
                              <img class="img_flashsale" src="{%IMGV2.item.img.390x0%}" alt="{%AIMGV2.item.img.alt%}" title="{%AIMGV2.item.img.title%}">
                              </a>
                         @endforeach
                    </td>
                    <td>
                         {{$itemMain->number_buyer_remind}}
                    </td>
                    <td>
                         {{$itemMain->number_click_mouse_view}}
                    </td>
                    @foreach($simpleShow as $show)
                         @if($show->name == 'act')
                              <?php 
                              $viewView = 'vh::ctview.'.strtolower(FCHelper::er($show,'type_show'));
                              $viewView = View::exists($viewView)?$viewView:"vh::ctview.base";
                              ?>
                              @include($viewView,array('item'=>$show,'dataItem'=>$itemMain))
                         @endif
				@endforeach
                    <td data-title="{{trans('db::function')}}" style="min-width: 130px;" class="action">
                         <a href="{{$admincp}}/viewdetail/{{$tableData->get('table_map','')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}"><i class="fa fa-eye" aria-hidden="true"></i>
						<span class="tooltiptext">Chi tiết </span>
					</a>
					@if($has_copy)
					<a href="{{$admincp}}/copy/{{$tableData->get('table_map','')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}"><i class="fa fa-copy" aria-hidden="true"></i>
						<span class="tooltiptext">Copy</span>
					</a>
					@endif
					@if($has_update)
					<a href="{{$admincp}}/edit/{{$tableData->get('table_map','')}}/{{FCHelper::ep($itemMain,'id')}}?returnurl={{$urlFull}}" class="{{trans('db::edit')}} tooltipx {{$tableData->get('table_map','')}}"><i class="fa fa-pencil" aria-hidden="true"></i>
						<span class="tooltiptext">Sửa</span>
					</a>
					@endif
					@if($has_trash)
					<a href="{{$admincp}}/{{isset($trash)?'backtrash':'trash'}}/{{$tableData->get('table_map','')}}" class="_vh_{{isset($trash)?'backtrash':'trash'}} tooltipx {{trans('db::delete')}} {{$tableData->get('table_map','')}}"><i class="fa fa-{{isset($trash)?'level-up':'trash'}}" aria-hidden="true"></i>
						<span class="tooltiptext">{{isset($trash)?'Restore':'Thùng rác'}}</span>
					</a>
					@endif
					@if($has_delete)
					<a href="{{$admincp}}/delete/{{$tableData->get('table_map','')}}" class="_vh_delete_permanent _vh_delete tooltipx {{trans('db::delete')}} {{$tableData->get('table_map','')}}"><i class="fa fa-times-circle" aria-hidden="true"></i>
						<span class="tooltiptext">Xóa vĩnh viễn</span>
					</a>
					@endif
				</td>
               </tr>
			@endfor
		</tbody>
	</table>
	<div class="pagination col-xs-12 m0 textcenter show aclr">
		<span class="total inlineblock pull-left">{{trans('db::number_record')}}:<strong> {{$listData->total()}}</strong></span>
		<div class="inlineblock pull-right">
			{{$listData->links()}}
		</div>
	</div>
</div>