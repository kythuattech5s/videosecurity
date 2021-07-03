<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code?$default_code:array();
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
}
 ?>
<div class="form-group">
  <p class="form-title" for="">{{FCHelper::er($table,'note')}}<span class="count"></span></p>
  <textarea  {{FCHelper::ep($table,'require')==1?'required':''}}  name="{{$name}}" placeholder="{{FCHelper::ep($table,'note')}}" dt-type="{{FCHelper::ep($table,'type_show')}}" class="form-control" rows="5">{{$value}}</textarea>
</div>
<script type="text/javascript">
	$(function() {
		@foreach($default_code as $dc)
		<?php 
		$source = $dc['source'];
		$source = $source=='this'?"textarea[name=$name]":$source;
		 ?>
		$(document).on('input', "{{$source}}", function(event) {
			event.preventDefault();
			@if($dc['function']=='count')
				var input = $(this).val();
				$(this).parent().find('span.count').text(input.length+" Chars");
			@endif
			@if($dc['function']=='seo_title')
				$('textarea[name={{$name}}]').val($(this).val());
			@endif
			@if($dc['function']=='seo_desc')
				$('textarea[name={{$name}}]').val($(this).val());
			@endif
			@if($dc['function']=='seo_key')
				$('textarea[name={{$name}}]').val($(this).val());
			@endif
		});
		@endforeach
	});
</script>