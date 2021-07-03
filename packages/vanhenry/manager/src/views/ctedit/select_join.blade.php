</style>
<?php 
$name = FCHelper::er($table,'name');
$default_data = FCHelper::er($table,'default_data');
$default_data = json_decode($default_data,true);
$arrData = FCHelper::er($default_data,'data');
$arrConfig = FCHelper::er($default_data,'config');
$source = FCHelper::er($arrConfig,'source');
$tableMap = $arrData['table'];
$dataMap = vanhenry\manager\helpers\DetailTableHelper::getAllDataOfTable($tableMap);
?>
@if($actionType == 'edit' || $actionType == 'copy')
	<?php
	$properties = array_filter(explode(',', $dataItem->content2));
	$properties = \Db::table('properties')->whereIn('id', $properties)->get();
	$propertyNotExistRecord = [];
	foreach($dataMap as $k => $v){
		foreach($properties as $k2 => $v2){
			if($v2->property_id == $k){
				unset($dataMap[$k]);
			}
		}
	}
	?>
	<input type="hidden" name="{{$name}}" value="{{$dataItem->content2}}">
	<p class="form-title">{{FCHelper::er($table,'note')}}</p>
	<div class="{{$name}}_join">
		<ul>
			@foreach($properties as $k => $v)
				<li>
					<label>
						<span><strong>{{$v->name}}</strong></span>
						<input type="hidden" class="name_join" value="{{$v->name}}">
						<input type="hidden" class="name_join_en" value="{{$v->en_name}}">
						<input type="checkbox" value="{{$v->id}}" class="id_join" checked>
						<input type="text" class="content_join" value="{{$v->content}}">
						<input type="text" class="content_join_en" value="{{$v->en_content}}">
					</label>
				</li>
			@endforeach
			@foreach($dataMap as $k2 => $v2)
				<li>
					<label>
						<span><strong>{{$v2->name}}</strong></span>
						<input type="hidden" class="name_join" value="{{$v2->name}}">
						<input type="hidden" class="name_join_en" value="{{$v2->en_name}}">
						<input type="checkbox" value="{{$v2->id}}" class="id_join">
					</label>
				</li>
			@endforeach
		</ul>
		@if(count($dataMap) > 0)
			<span class="submit bgmain">Lưu</span>
		@endif
	</div>
@else
	<input type="hidden" name="{{$name}}">
	<p class="form-title">{{FCHelper::er($table,'note')}}</p>
	<div class="{{$name}}_join">
		<ul>
			@foreach($dataMap as $k => $v)
				<li>
					<label>
						<span><strong>{{$v->name}}</strong></span>
						<input type="hidden" class="name_join" value="{{$v->name}}">
						<input type="hidden" class="name_join_en" value="{{$v->en_name}}">
						<input type="checkbox" value="{{$v->id}}" class="id_join">
					</label>
				</li>
			@endforeach
		</ul>
		@if(count($dataMap) > 0)
			<span class="submit bgmain">Lưu</span>
		@endif
	</div>
@endif
<style type="text/css">
.{{$name}}_join {
    border: 1px solid green;
    padding: 10px;
    margin-bottom: 15px;
}

.{{$name}}_join ul li {
    margin-bottom: 10px;
    border-bottom: 1px solid #cdcdcd;
    padding-bottom: 5px;
}
.{{$name}}_join ul li span, .{{$name}}_join ul li input {
	vertical-align: middle;
    margin-top: 0px;
    margin-right: 5px;
}
.{{$name}}_join ul li label {
	font-weight: normal;
}
.{{$name}}_join .submit {
    color: #fff;
    width: 50px;
    height: 30px;
    display: inline-block;
    border-radius: 3px;
    line-height: 30px;
    text-align: -webkit-center;
    cursor: pointer;
    -webkit-transition: all 0.3s ease 0s, transform 0.3s;
    -moz-transition: all 0.3s ease 0s, transform 0.3s;
    -ms-transition: all 0.3s ease 0s, transform 0.3s;
    -o-transition: all 0.3s ease 0s, transform 0.3s;
    transition: all 0.3s ease 0s, transform 0.3s;
}

.{{$name}}_join .submit:hover {
    background: #E96A0C;
}
</style>
<script type="text/javascript">
$('.{{$name}}_join ul li label input.id_join').change(function(event) {
	if($(this).is(':checked')){
		$(this).parent().parent().append('<input type="text" class="content_join" placeholder="Mô tả thuộc tính"><input type="text" class="content_join_en" placeholder="Mô tả thuộc tính(en)">')
	}
	else{
		$(this).parent().parent().find('.content_join').remove();
		$(this).parent().parent().find('.content_join_en').remove();
	}
});
$('.{{$name}}_join .submit').click(function(event) {
	var inputs = $('.{{$name}}_join ul li input:checked');
	var data = [];
	inputs.each(function(index, el) {
		var obj = {};
		obj.property_id = $(el).val();
		obj.property_name = $(el).parent().parent().find('.name_join').val();
		obj.property_name_en = $(el).parent().parent().find('.name_join_en').val();
		obj.property_content = $(el).parent().parent().find('.content_join').val();
		obj.property_content_en = $(el).parent().parent().find('.content_join_en').val();
		data.push(obj);
	});
	$.ajax({
		url: 'esystem/them-thuoc-tinh',
		type: 'post',
		dataType: 'json',
		global: false,
		data: {json: JSON.stringify(data)},
	})
	.done(function(json) {
		if(json.code == 200){
			$('input[name={{$name}}]').val(json.id);
			$.simplyToast(json.message, 'success');
		}
		else $.simplyToast(json.message, 'danger');
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
	
});
</script>