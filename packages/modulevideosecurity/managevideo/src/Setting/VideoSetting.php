<?php
namespace modulevideosecurity\managevideo\Setting;
use modulevideosecurity\managevideo\Setting\VideoSettingInferface;
class VideoSetting implements VideoSettingInferface
{
	protected $baseSetting = array(
		'path_output_folder' => 'tvsout'
	);
	protected $extAcceptConvert = ['mp4'];
    public function setKeyUrlResolver($key){
    	return route('tvs-video.key', ['key' => $key]);
    }
	public function setMediaUrlResolver($mediaFilename){
    	return \Storage::disk('tvsvideos')->url($this->baseSetting['path_output_folder'].'/'.$mediaFilename);
    }
    public function setPlaylistUrlResolver($playlistFilename){
    	return route('tvs-video.playlist', ['playlist' => $playlistFilename]);
    }
    public function getSettingConfig($key){
    	return isset($this->baseSetting[$key]) ? $this->baseSetting[$key]:'';
    }
    public function jsonDecode($data){
		$result = json_decode($data,true);
		return @$result?$result:[];
	} 
    public function createTvsSecret($itemMedia){
    	$fileInfo = $this->jsonDecode($itemMedia->extra);
    	if (count($fileInfo) == 0) return;
    	if (!in_array($fileInfo['extension'])) return;
    	$dataCreate = array(
    		'media_id' 		=> $itemMedia->id,
			'file_name' 	=> $itemMedia->file_name,
			'file_path' 	=> $itemMedia->path,
			'playlist_name' => str_replace($fileInfo['extension'],'m3u8',$itemMedia->file_name),
			'playlist_path' => 'public/tech5s_security_videos/'.$this->baseSetting['path_output_folder'].'/',
			'converted' 	=> 0,
			'created_at' 	=> new \DateTime,
			'updated_at' 	=> new \DateTime
    	);
    	dd($dataCreate);
    }
}