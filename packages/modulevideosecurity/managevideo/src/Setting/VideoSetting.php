<?php
namespace modulevideosecurity\managevideo\Setting;
use modulevideosecurity\managevideo\Setting\VideoSettingInferface;
use modulevideosecurity\managevideo\Models\{TvsSecret,TvsMapItem};
use \modulevideosecurity\managevideo\Jobs\ConvertVideoForStreaming;
class VideoSetting implements VideoSettingInferface
{
	protected $baseSetting = array(
		'path_output_folder' => 'tvsout'
	);
	protected $extAcceptConvert = ['mp4'];
    public function setKeyUrlResolver($key,$itemTvsSecret){
    	return route('tvs-video.key', ['key' => $key]).'?info='.$itemTvsSecret->media_id;
    }
    public function checkHaveAccess($itemTvsSecret)
    {
        return true;
    }
	public function setMediaUrlResolver($mediaFilename,$fileDiskPath){
    	return \Storage::disk('tvsvideos')->url($fileDiskPath.$mediaFilename);
    }
    public function setPlaylistUrlResolver($playlistFilename,$itemTvsSecret){
    	return route('tvs-video.playlist', ['playlist' => $itemTvsSecret->media_id]).'?info='.$playlistFilename;
    }
    public function getSettingConfig($key){
    	return isset($this->baseSetting[$key]) ? $this->baseSetting[$key]:'';
    }
    public function jsonDecode($json){
		@json_decode($json);
        if (json_last_error() != JSON_ERROR_NONE) return array();
        return json_decode($json,true);
	} 
    public function createTvsSecret($itemMedia){
        if (!isset($itemMedia)) return;
    	$fileInfo = $this->jsonDecode($itemMedia->extra);
    	if (count($fileInfo) == 0) return;
    	if (!in_array($fileInfo['extension'],$this->extAcceptConvert)) return;
        $itemTvsSecrets = new TvsSecret;

    	$itemTvsSecrets->media_id 	   = $itemMedia->id;
		$itemTvsSecrets->file_name 	   = $itemMedia->file_name;
		$itemTvsSecrets->file_path 	   = $itemMedia->path;
		$itemTvsSecrets->playlist_name = str_replace($fileInfo['extension'],'m3u8',$itemMedia->file_name);
		$itemTvsSecrets->playlist_path = 'public/tech5s_security_videos/'.$this->baseSetting['path_output_folder'].'/'.$itemMedia->id.'/';
        $itemTvsSecrets->disk_path     = $this->baseSetting['path_output_folder'].'/'.$itemMedia->id.'/';
		$itemTvsSecrets->converted        = 0;
		$itemTvsSecrets->created_at    = new \DateTime;
		$itemTvsSecrets->updated_at    = new \DateTime;
        $itemTvsSecrets->save();
    }
    public function deleteTvsSecret($itemMedia){
        if (!isset($itemMedia)) return;
        $fileInfo = $this->jsonDecode($itemMedia->extra);
        if (count($fileInfo) == 0) return;
        if (!in_array($fileInfo['extension'],$this->extAcceptConvert)) return;
        $itemTvsSecrets = TvsSecret::where('media_id',$itemMedia->id)->get()->first();
        if (!isset($itemTvsSecrets)) return;
        $this->deleteDir($itemTvsSecrets->playlist_path);
        $itemTvsSecrets->delete();
    }
    public function catchInsertAdminEvent($table,array $data,int $targetId){
        $videoIds = $this->getVideoIdFromDataArray($data);
        $this->insertVideoMapTable($table,$videoIds,$targetId);
    }
    public function catchUpdateAdminEvent($table,array $oldĐata,object $newData){
        $oldVideoIds = $this->getVideoIdFromDataArray($oldĐata);
        $newVideoIds = $this->getVideoIdFromDataObj($newData);
        $this->deleteVideoMapTable($table,$oldVideoIds,$newData->id);
        $this->insertVideoMapTable($table,$newVideoIds,$newData->id);
    }
    public function catchDeletetAdminEvent($table,$id){
        $ids = explode(',', $id);
        TvsMapItem::where('table_name',$table)->whereIn('target_id',$ids)->delete();
    }
    public function deleteVideoMapTable($table,$listVideoId,$targetId){
        if (count($listVideoId) == 0) return;
        TvsMapItem::where('table_name',$table)->where('target_id',$targetId)->whereIn('video_meida_map_id',$listVideoId)->delete();
    }
    public function insertVideoMapTable($table,$listVideoId,$targetId){
        if (count($listVideoId) == 0) return;
        $data = array();
        foreach ($listVideoId as $videoId) {
            $dataAdd = array(
                'table_name'    => $table,
                'target_id'     => $targetId,
                'video_meida_map_id'  => $videoId,
                'created_at'    => new \DateTime,
                'updated_at'    => new \DateTime
            );
            array_push($data,$dataAdd);
        }
        TvsMapItem::insert($data);
    }
    public function getVideoIdFromDataArray($arr){
        $ret = [];
        foreach ($arr as $key => $value) {
            if ($this->isJsonMediaVideo($value)) {
                $valueInfo = $this->jsonDecode($value);
                array_push($ret,$valueInfo['id']);
            }
        }
        return $ret;
    }
    public function getVideoIdFromDataObj($obj){
        return $this->getVideoIdFromDataArray(get_object_vars($obj));
    }
    public function isJsonMediaVideo($value){
        $valueInfo = $this->jsonDecode($value);
        if (!is_array($valueInfo)) return false;
        if (count($valueInfo) == 0) return false;
        if (isset($valueInfo['id']) && isset($valueInfo['file_name']) && isset($valueInfo['path']) && isset($valueInfo['extra'])) {
            return true;
        }
        return false;
    }
    public function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            return "$dirPath must be a directory";
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}
    