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
  <p class="form-title" for="">{{FCHelper::ep($table,'note')}} <span class="count"></span></p>
  <input  {{FCHelper::ep($table,'require')==1?'required':''}} type="text" name="{{$name}}" placeholder="{{FCHelper::er($table,'note')}}"  class="form-control" dt-type="{{FCHelper::er($table,'type_show')}}" value="{{$value}}" />
</div>
<script type="text/javascript">
	$(function() {
		$(document).on('change', 'input[name={{$name}}]', function(event) {
			event.preventDefault();
			var val = $(this).val();
			if(val.indexOf("$")==0){
				return;
			}
			var _this = this;
			$.ajax({
				url: '{{$admincp}}/getCrypt',
				type: 'POST',
				data: {pass: $(this).val()},
			})
			.done(function(data) {
				$(_this).val(data);
			});
			
		});
	});
</script>