<?php 

$name = FCHelper::er($table,'name');

$default_code = FCHelper::er($table,'default_code');

$default_code = json_decode($default_code,true);

$default_code = @$default_code?$default_code:array();

$value ="";

$img = 'admin/images/noimage.png';

if($actionType=='edit'||$actionType=='copy'){

	$value = FCHelper::ep($dataItem,$name);

	$tmp = json_decode($value,true);

	$img = isset($tmp) && is_array($tmp) && array_key_exists("path", $tmp)  ?$tmp["path"].$tmp["file_name"]:$img; 

}

?>
<div class="row">
	<p class="des col-xs-12">{{trans('db::choose_img')}} {{FCHelper::ep(($tableMap=='configs'?$dataItem:$table),'note')}}</p>
	<div class="col-xs-12">
		<img style="    margin: 0 auto;max-width: 30%;" src="{{($tableData->get('table_map','')=='transactions'?$value:$img)}}" alt="" class="img-responsive">
		<input placeholder="{{FCHelper::er($table,'note')}}"  type="hidden" value="{{$value}}" name="{{$name}}" id="{{$name}}">
		<div class="form-group textcenter">
			<a href="{{$admincp}}/media/view?istiny={{$name}}" class="browseimage bgmain btn btn-primary iframe-btn" type="button">{{trans('db::choose_img')}}</a>
			<button style="margin-top: 15px;margin-left: 5px;" class="btnchange-{{$name}} bgmain btn btn-primary">{{trans('db::edit')}}</button>
			<button style="margin-top: 15px;margin-left: 5px;" class="btndelete-{{$name}} bgmain btn btn-primary">{{trans('db::delete')}}</button>
		</div>
	</div>
</div>



<script type="text/javascript">

	$(function() {
		$('.btndelete-{{$name}}').click(function(event) {
			event.preventDefault();
			$("input[name='{{$name}}']").val("");
			$("input[name='{{$name}}']").prev().attr("src","");
		});
		$(".btnchange-{{$name}}").click(function(event) {

			event.preventDefault();

			var file = $('input[name={{$name}}]').val();

			try{

				file = JSON.parse(file);	

			}

			catch(ex){

				file = {};

				file.title="";

				file.caption="";

				file.alt="";

				file.description="";

			}

			

			var dialog = bootbox.dialog({

				title:"Chỉnh sửa thông tin ảnh",

				onEscape:true,

				message: '<div class="row">'

				+'<div class="col-md-6 col-xs-12 form-group">'

				+'	<label for="">Title</label>'

				+'	<input name="title" type="text" class="form-control" value="'+(file.title==null?'':file.title)+'" placeholder="Title">'

				+'	</div>'

				+'	<div class="col-md-6 col-xs-12 form-group">'

				+'		<label for="">Caption</label>'

				+'		<input name="caption" type="text" class="form-control" value="'+(file.caption==null?'':file.caption)+'" placeholder="Caption">'

				+'	</div>'

				+'	<div class="col-md-6 col-xs-12 form-group">'

				+'		<label for="">Alt</label>'

				+'		<input name="alt" type="text" class="form-control" value="'+(file.alt==null?'':file.alt)+'" placeholder="Alt">'

				+'	</div>'

				+'	<div class="col-md-6 col-xs-12 form-group">'

				+'		<label for="">Description</label>'

				+'		<input name="description" type="text" class="form-control" value="'+(file.description==null?'':file.description)+'" placeholder="Description">'

				+'	</div>'

				+'</div>',

				buttons: {

					confirm: {

						label: '{{trans('db::save')}}',

						className: 'btn-success',

						callback: function() {

							file.title = dialog.find("input[name=title]").val();

							file.caption = dialog.find("input[name=caption]").val();

							file.alt = dialog.find("input[name=alt]").val();

							file.description = dialog.find("input[name=description]").val();

							$('input[name={{$name}}]').val(JSON.stringify(file));

						}

					},

					cancel: {
						label: '{{trans('db::close')}}',
						callback:function(){

							dialog.modal("hide");

						}

					}

				},

			});

			dialog.modal("show");

		});

	});



</script>