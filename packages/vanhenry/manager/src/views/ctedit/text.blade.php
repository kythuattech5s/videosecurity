<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code?$default_code:array();
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	if($name != 'price' && $name != 'price_sale')
	$value = FCHelper::er($dataItem,$name);
	else
	$value = $dataItem->$name;
}
?>
<div class="form-group" @if($tableMap=='orders' && $name=='val_order') style="display: none;" @endif>
	<p class="form-title" for="">{{FCHelper::er($table,'note')}} <span class="count"></span></p>
	<input  {{FCHelper::ep($table,'require')==1?'required':''}} type="text" name="{{$name}}" placeholder="{{FCHelper::ep($table,'note')}}"  class="form-control" dt-type="{{FCHelper::ep($table,'type_show')}}" value="{{$value}}" />
</div>
<script type="text/javascript">
	$(function() {
		@foreach($default_code as $dc)
		<?php 
		$source = $dc['source'];
		$source = $source=='this'?"input[name=$name]":$source;
		?>
		$(document).on('input', "{{$source}}", function(event) {
			event.preventDefault();
			@if($dc['function']=='count')
			var input = $(this).val();
			$(this).parent().find('span.count').text(input.length+" Chars");
			@endif
			@if($dc['function']=='seo_title')
			$('input[name={{$name}}]').val($(this).val());
			@endif
			@if($dc['function']=='seo_desc')
			$('input[name={{$name}}]').val($(this).val());
			@endif
			@if($dc['function']=='seo_key')
			$('input[name={{$name}}]').val($(this).val());
			@endif
		});
		@endforeach
	});
</script>