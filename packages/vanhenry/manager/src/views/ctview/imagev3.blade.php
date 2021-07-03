<?php

$img_null = 'admin/images/noimage.png';

$value = FCHelper::ep($dataItem,$show->name);

$tmps = json_decode($value,true);


$imgs = [];
if(isset($tmps) && is_array($tmps)){
	foreach($tmps as $tmp){
		$image = $tmp["path"].$tmp["file_name"];
		if(array_key_exists("path", $tmp)){
			$imgs[] = $image;
		}else{
			$imgs[] = null;			
		}
	}
}
?>
<td data-title="{{$show->note}}" class="gallerys">
	@if($imgs == null)
	<img src="{{$img_null}}" style="max-width: 70px;max-height: 70px;margin: 2px auto;" class="img-responsive">
	@else
		@foreach($imgs as $img)
		<a href="{{$img}}" rel="gallery-box" class="preview">
			<img src="{{$img}}" style="max-width: 70px;max-height: 70px;margin: 2px;" class="img-responsive">
		</a>
		@endforeach
	@endif
</td>

<script type="text/javascript">
	$("a.preview").fancybox({ 
		speedIn: 600, 
		speedOut: 200, 
		overlayShow: false,
		autoScale: true
	});
</script>