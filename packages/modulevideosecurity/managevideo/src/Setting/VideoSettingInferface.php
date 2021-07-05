<?php
namespace modulevideosecurity\managevideo\Setting;
interface VideoSettingInferface{
	public function checkHaveAccess($itemTvsSecret);
	public function setKeyUrlResolver($key,$itemTvsSecret);
	public function setMediaUrlResolver($mediaFilename,$fileDiskPath);
    public function setPlaylistUrlResolver($playlistFilename,$itemTvsSecret);
}