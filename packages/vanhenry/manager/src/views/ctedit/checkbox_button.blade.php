<?php 
$name = FCHelper::er($table,'name');
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::ep($dataItem,$name);
}
?>
<div class="form-group">
  <p class="form-title" for="">{{FCHelper::ep($table,'note')}} <span class="count"></span></p>
  <input placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}}  class="ccb _{{$name}}" type="checkbox" data-off-label="false" data-on-label="false" data-off-icon-cls="glyphicon-remove" {{$value == 1?'checked':''}} data-on-icon-cls="glyphicon-ok">
  <input type="hidden" name="{{$name}}" value="{{$value}}">
</div>
<script type="text/javascript">
	$(function() {
		$('input._{{$name}}').change(function(event) {
			var v = $(this).is(':checked')?1:0;
			$('input[name={{$name}}]').val(v);
		});
	});
</script>