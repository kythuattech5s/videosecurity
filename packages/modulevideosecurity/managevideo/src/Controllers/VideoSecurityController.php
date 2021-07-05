<?php
namespace modulevideosecurity\managevideo\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use \vanhenry\manager\model\Media;
use modulevideosecurity\managevideo\Models\{TvsSecret,TvsMapItem};

class VideoSecurityController extends Controller
{
	protected $videoSetting;
	public function __construct(\modulevideosecurity\managevideo\Setting\VideoSettingInferface $videoSetting){
		$this->videoSetting = $videoSetting;
	}
    public function playVideo($playList)
    {
    	$itemTvsSecret = TvsSecret::where('media_id',$playList)->get()->first();
    	$fileName = request()->input('info');
    	if (!isset($itemTvsSecret) || (int)$itemTvsSecret->converted == 0) return 'Đã xảy ra lỗi trong quá trình xử lý!';
    	if (!$this->videoSetting->checkHaveAccess($itemTvsSecret)) {
    		return 'Not have accesN';
    	}
    	$fileDiskPath = $itemTvsSecret->disk_path;
    	if (isset($fileName)) {
    		$filePath = $fileDiskPath.$fileName;
    	}else {
    		$filePath = $fileDiskPath.$itemTvsSecret->playlist_name;
    	}
    	return \FFMpeg::dynamicHLSPlaylist()
		->fromDisk('tvsvideos')
		->open($filePath)
		->setKeyUrlResolver(function ($key) use ($itemTvsSecret) {
	        return $this->videoSetting->setKeyUrlResolver($key,$itemTvsSecret);
	    })
	    ->setMediaUrlResolver(function ($mediaFilename) use ($fileDiskPath) {
	        return $this->videoSetting->setMediaUrlResolver($mediaFilename,$fileDiskPath);
	    })
	    ->setPlaylistUrlResolver(function ($playlistFilename) use ($itemTvsSecret){
	        return $this->videoSetting->setPlaylistUrlResolver($playlistFilename,$itemTvsSecret);
	    });
    }
    public function key($key){
    	$mediaId = (int)request()->input('info');
    	$itemTvsSecret = TvsSecret::where('media_id',$mediaId)->get()->first();
    	if (!isset($itemTvsSecret) || (int)$itemTvsSecret->converted == 0) return 'Đã xảy ra lỗi trong quá trình xử lý!';
    	return \Storage::disk('tvsvideos')->download($itemTvsSecret->disk_path.$key);
    }
    public function autoConvertTvs()
    {
        \Artisan::call('tvsvideo:convert');
    }
}