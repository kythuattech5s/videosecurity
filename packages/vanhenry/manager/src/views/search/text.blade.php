<div class="search-item search-{{$item->name}} search-type-text flex">
	<select class="simpleselect2" name="{{$item->name}}-compare">
		<option value="relative" {{isset($dataSearch['search-'.$item->name]) && $dataSearch['search-'.$item->name] == 'relative' ? 'selected' : ''}}>~=</option>
		<option value="absolute" {{isset($dataSearch['search-'.$item->name]) && $dataSearch['search-'.$item->name] == 'absolute' ? 'selected' : ''}}>=</option>
	</select>
	<input type="text" value="{{$dataSearch[$item->name] ?? ''}}" name="{{$item->name}}" placeholder="{{$item->note}}" />
</div>