<?php 
$name = FCHelper::er($table,'name');
$defaultData = FCHelper::er($table,'default_data');
$arrKey = json_decode($defaultData,true);

$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::ep($dataItem,$name);
}
?>
<div class="form-group ">
  <p class="form-title" for="">{{FCHelper::ep($table,'note',1)}}<p/>
  <p>{{FCHelper::ep($table,'more_note',1)}}</p>
  <div class="form-group textcenter">
	<strong>{{$value==1?"Câu hỏi đã được lựa chọn làm đáp án đúng":""}}</strong>
  	<input type="hidden" name="{{$name}}" value="0">
	<button style="margin-top: 8px;" class="btnrefer-{{$name}} bgmain btn btn-primary" type="button">Cập nhật</button>
</div>
</div>
<script type="text/javascript">
	$(document).on('click', '.btnrefer-{{$name}}', function(event) {
		event.preventDefault();
		$("input[name={{$name}}]").val(1);
		$.ajax({
			url: '{{$admincp}}/updateRefer/{{FCHelper::er($arrKey,"table")}}',
			type: 'POST',
			dataType: 'json',
			data: {data: '{!!json_encode($arrKey)!!}',form:$("#frmUpdate").serialize()},
		})
		.done(function() {
		});
		
	});

</script>