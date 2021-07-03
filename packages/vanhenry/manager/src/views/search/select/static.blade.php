<div class="search-item search-{{$item->name}} search-type-text flex">
	<select style="width:100%" data-placeholder="Vui lòng nhập tên {{$item->note}}" class=" ajx_search_{{$item->name}}" name="{{$item->name}}">
		@foreach($data as $k => $value)
		<option value='{{$value['key']}}' {{isset($dataSearch[$item->name]) && $dataSearch[$item->name] == $k ? 'selected' : ''}}>{{$value['vi_value']}}</option>
		@endforeach
	</select>
</div>