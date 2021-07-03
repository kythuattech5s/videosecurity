<?php
$isAjax = $config["ajax"];
$table= $data["table"];
$dataMapDefault= $data["default"];
?>
<div class="search-item search-{{$item->name}} search-type-text flex">
	<select style="width:100%" data-placeholder="Vui lòng nhập tên {{$item->note}}" class=" ajx_search_{{$item->name}}" name="{{$item->name}}">
	</select>
</div>
@if($isAjax)
<script type="text/javascript">
	$(function() {
		$('.ajx_search_{{$item->name}}').select2({
			ajax: {
				url: "{{$admincp}}/getData/{{$table}}",
				dataType: 'json',
				data: function (params) {
					return {
						q: params.term, 
						page: params.page
					};
				},
				processResults: function (data, page) {
					return data;
				},
				cache: true
			},
			minimumInputLength: 1,
			language:"{{App::getLocale()}}",
		});
	});
</script>
@else
<script type="text/javascript">
	$(function() {
		$.ajax({
			url: '{{$admincp}}/getRecursive/{{$table}}',
			type: 'POST',
			data: {data: '{!!json_encode($arrKey)!!}'},
		})
		.done(function(html) {
			$('.ajx_search_{{$item->name}}').html(html);
		})
		;
	});
</script>
@endif