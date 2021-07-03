<div class="search-item search-{{$item->name}} search-type-text flex">
	<select class="simpleselect2" name="{{$item->name}}-compare">
		<option value="relative">~=</option>
		<option value="absolute">=</option>
	</select>
	<input type="text" name="{{$item->name}}" placeholder="{{$item->note}}" />
</div>
