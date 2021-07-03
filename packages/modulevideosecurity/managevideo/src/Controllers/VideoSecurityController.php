<?php
namespace modulevideosecurity\managevideo\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class VideoSecurityController extends Controller
{
	protected $videoSetting;
	public function __construct(\modulevideosecurity\managevideo\Setting\VideoSettingInferface $videoSetting){
		$this->videoSetting = $videoSetting;
	}
    public function playVideo($playlist)
    {
    	return \FFMpeg::dynamicHLSPlaylist()
		->fromDisk('tvsvideos')
		->open($this->videoSetting->getSettingConfig('path_output_folder').'/'.$playlist)
		->setKeyUrlResolver(function ($key) {
	        return $this->videoSetting->setKeyUrlResolver($key);
	    })
	    ->setMediaUrlResolver(function ($mediaFilename) {
	        return $this->videoSetting->setMediaUrlResolver($mediaFilename);
	    })
	    ->setPlaylistUrlResolver(function ($playlistFilename) {
	        return $this->videoSetting->setPlaylistUrlResolver($playlistFilename);
	    });
    }
    public function key($key){
    	return \Storage::disk('tvsvideos')->download($this->videoSetting->getSettingConfig('path_output_folder').'/'.$key);
    }
}