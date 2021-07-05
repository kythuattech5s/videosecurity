<div style="">
	@php
		$fileInfo = \VideoSetting::jsonDecode($video_info);
		$id = isset($fileInfo['id']) ? (int)$fileInfo['id']:0;
	@endphp
	<video-js id="my_video_1" class="video-js vjs-default-skin vjs-16-9" controls preload="auto" width="640" height="268">
		<source src="{{route('tvs-video.playlist',['playlist'=>$id])}}" type="application/x-mpegURL">
	</video-js>
</div>