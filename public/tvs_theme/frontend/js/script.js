(function(window, videojs) {
	var player = window.player = videojs('my_video_1',{
		playbackRates: [0.25 ,0.5 , 1, 1.5, 2]
	});
	player.hlsQualitySelector({
		displayCurrentQuality: true,
	});
}(window, window.videojs));