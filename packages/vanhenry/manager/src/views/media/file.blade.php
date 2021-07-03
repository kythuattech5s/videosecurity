<div id="file{{$file->id}}" class="col-md-2 col-sm-3 col-xs-6 media-it file fileitem" data-file='{{json_encode($file)}}'>
<?php $extra = json_decode($file->extra,true); ?>
	<div class="media-item">
		<div class="dp-table">
			<a class="mdi-img" title="" href="{{$file->path.$file->file_name}}" rel="mdi">
				<img class="lazy" src="{{$extra['thumb']}}" alt="">
			</a>
		</div>
		<div class="mdi-check">
			<label><input class="selectfile" value="{{$file->id}}" type="checkbox"><i class="fa fa-check-square-o"></i></label>
		</div>
		<div class="mdi-btn clearfix">
			<a download href="{{$file->path.$file->file_name}}" data-toggle="tooltip" title="Tải về"><i class="fa fa-download"></i></a>
			<a href="{{$file->path.$file->file_name}}" rel="gallery-box" class="preview"><i class="fa fa-eye"></i></a>
			<a class="name-edit" dt-id="{{$file->id}}" href="#" data-toggle="tooltip" title="Đổi tên"><i class="fa fa-pencil"></i></a>
			@if($trash==0)
			<a onclick="MediaManager.getDetailFile({{$file->id}});return false;" dt-id="{{$file->id}}" href="#" data-toggle="tooltip" title="Thông tin file"><i class="fa fa-info-circle"></i></a>
			@else
			<a onclick="MediaManager.restore({{$file->id}});return false;" dt-id="{{$file->id}}" href="#" data-toggle="tooltip" title="Thông tin file"><i class="fa fa-arrow-up"></i></a>
			@endif
			<a href="#" dt-id="{{$file->id}}" onclick="{{$trash==1?'MediaManager.deleteFileFull(this)':'MediaManager.deleteFile(this)'}};return false;" data-toggle="tooltip" title="Xóa"><i class="fa fa-trash-o"></i></a>
		</div>
		<p class="mdi-title">{{substr($file->name,0,strrpos($file->name,"."))}}</p>	
		<span class="mdi-date">{{$file->created_at}}</span>
		<span class="mdi-size">{{$extra['size']}}</span>
	</div>
</div>