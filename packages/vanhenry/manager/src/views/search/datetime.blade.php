<div class="search-item search-date search-{{$item->name}} search-type-text flex">
	<div class="row m0 flex sdate" style="position: relative;">
		<input  class="datepicker textcenter" placeholder="Từ" type="text" style="overflow: hidden;padding-right: 18px;" value="{{$dataSearch['from-'.$item->name] ?? ''}}" />
		<input type="hidden" name="from-{{$item->name}}" value="{{$dataSearch['from-'.$item->name] ?? ''}}">
		<span style="display: block;height: 1px;background: #000;width: 13px;z-index: 9999;position: absolute;left: 45%; top:50%"></span>
		<input  class="datepicker textcenter" placeholder="Tới" type="text" style="overflow: hidden;padding-right: 18px;" value="{{$dataSearch['to-'.$item->name] ?? ''}}" />
		<input type="hidden" name="to-{{$item->name}}" value="{{$dataSearch['to-'.$item->name] ?? ''}}">
	</div>
</div>