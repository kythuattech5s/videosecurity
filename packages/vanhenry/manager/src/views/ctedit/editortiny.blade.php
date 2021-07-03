<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code&& count($default_code)>0?$default_code[0]:array();
$height = FCHelper::er($default_code,'height');
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
}
 ?>
<div class="form-group">
  <p class="form-title" for="">{{FCHelper::er($table,'note')}}</p>
  <textarea placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}}  dt-height="{{$height}}" name="{{$name}}" class="form-control editortiny{{$name}}" rows="5" dt-type="{{FCHelper::er($table,'type_show')}}">{{$value}}</textarea>
</div>
<script type="text/javascript">
	$(function() {
		$('.editortiny{{$name}}').tinymce({
	        height: 100,
	        theme: 'modern',
	        menubar:'',
	        plugins: [
	          'code advlist autolink lists link paste textcolor colorpicker',
	        ],
	        toolbar1:"code bold italic underline hr strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | fontsizeselect | link unlink anchor table | forecolor backcolor pastetext",
	        document_base_url:'{{asset('/')}}',
	        image_advtab: true,
	        external_filemanager_path:'{{asset('/').$admincp}}'+'/media/view',
	        filemanager_title:"File Manager" ,
	        external_plugins: { "filemanager" : '{{asset('/')}}'+"public/plug/tinymce/plugins/tech5sfilemanager/plugin.min.js"}
	      
	    });
	});	
</script>