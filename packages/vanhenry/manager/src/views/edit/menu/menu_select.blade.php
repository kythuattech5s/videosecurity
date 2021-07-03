<?php 
$lang = \vanhenry\manager\CT::getLanguage();
$defaultdata = json_decode($tableData->get("default_data",""),true);
$showInMenu = $defaultdata["showinmenu"];
 ?>
<select class="menu-select">
	@foreach($showInMenu as $key => $item)
	<option value="{{$key}}">{{$item[$lang]}}</option>
	@endforeach
</select>
@foreach($showInMenu as $key => $item)
	@if($key =='static')
		
	@else if($key == 'out')
		<div class="data-select" data-select="link">
			<input type="text" placeholder="Nhập URL">
		</div>
	@else if($key=='home')
	@else
		<div class="data-select" data-select="{{$key}}">
			<input type="hidden">
			<button type="button" class="btn btn-default">Chọn tin tức</button>
			<div class="select-search">
			  <input type="text" placeholder="Tìm kiếm danh mục">
			  <ul>
			    <li data-value="sp1">Tin tức 1</li>
			    <li data-value="sp2">Tin tức 2</li>
			    <li data-value="sp3">Tin tức 3</li>
			    <li data-value="sp4">Tin tức 4</li>
			    <li data-value="sp5">Tin tức 5</li>
			  </ul>
			  <div class="select-pagination">
			    <a href="#" title="" class="sp-prev"><i class="fa fa-angle-left"></i></a>
			    <a href="#" title="" class="sp-next disable"><i class="fa fa-angle-right"></i></a>
			  </div>
			</div>
		</div>
	@endif
@endforeach


